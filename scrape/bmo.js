const puppeteer = require("puppeteer");
// const fs = require('fs');
require("dotenv").config();

const snooze = ms => new Promise(resolve => setTimeout(resolve, ms));

var conf = {
    password: process.env.SCRAPER_BMO_PASS,
    customerId: process.env.SCRAPER_BMO_CUSTOMER_ID,
    userId: process.env.SCRAPER_BMO_USER_ID
};

var proxyPort = process.env.SCRAPER_PROXY_PORT;
var proxyHost = process.env.SCRAPER_PROXY_HOST;
var proxyUser = process.env.SCRAPER_PROXY_USER;
var proxyPass = process.env.SCRAPER_PROXY_PASS;

process.on("uncaughtException", function(err) {
    log("Caught exception: " + err);
});

const verbose = process.argv.includes("--verbose");

const log = (...val) => {
    if (verbose) console.log("[LOGGER]: ", ...val);
};

(async () => {
    async function scrape(page) {
        let results = await page.evaluate(() => {
            function scrape() {
                let rows = extractRows();
                let details = extractDetails();
                return { transactionSummary: rows, transactionDetail: details };
            }

            function extractRows() {
                let rows = [];
                let columnNames = [
                    "valueDate",
                    "orderingCustomerName",
                    "amount",
                    "currency",
                    "beneficiaryAccountNumber",
                    "beneficiaryName"
                ];

                let rowEls = document.querySelectorAll(
                    "tr[style='height:8px']"
                );
                rowEls.forEach((rowEl, index) => {
                    let colEls = rowEl.querySelectorAll("td > span");
                    let row = {};
                    colEls.forEach((colEl, index) => {
                        row[columnNames[index]] = colEl.innerText;
                    });

                    rows.push(row);
                });

                return rows;
            }

            function extractDetails() {
                let rows = [];
                let row = {};

                let tableEls = document.querySelectorAll(
                    "tr[style='height: 1px; display: none;'] table[style='table-layout:fixed;width:100%;']"
                );
                tableEls.forEach((tableEl, tableIndex) => {
                    let rowEls = tableEl.querySelectorAll("tr");

                    rowEls.forEach((rowEl, rowIndex) => {
                        let name = rowEl.querySelector("td:nth-of-type(1)")
                            .innerText;
                        let value = rowEl.querySelector("td:nth-of-type(2)")
                            .innerText;

                        if (
                            name &&
                            name.trim() &&
                            value &&
                            value.trim() &&
                            name.length < 50
                        ) {
                            value = value.trim();
                            name = name.trim();

                            name = toCamelCaseString(name);
                            if (name == "referenceNumber")
                                lastReferenceNumber = value;
                            row[name] = value.trim();
                        }
                    });

                    if (tableIndex > 0 && tableIndex % 2 == 1) {
                        rows.push(row);
                        row = {};
                    }
                });

                return rows;
            }

            // util function to convert the input to string type
            function convertToString(input) {
                if (input) {
                    if (typeof input === "string") {
                        return input;
                    }

                    return String(input);
                }
                return "";
            }

            // convert string to words
            function toWords(input) {
                input = convertToString(input);
                var regex = /[A-Z\xC0-\xD6\xD8-\xDE]?[a-z\xDF-\xF6\xF8-\xFF]+|[A-Z\xC0-\xD6\xD8-\xDE]+(?![a-z\xDF-\xF6\xF8-\xFF])|\d+/g;
                return input.match(regex);
            }

            // convert the input array to camel case
            function toCamelCase(inputArray) {
                let result = "";
                for (let i = 0, len = inputArray.length; i < len; i++) {
                    let currentStr = inputArray[i];
                    let tempStr = currentStr.toLowerCase();

                    if (i != 0) {
                        // convert first letter to upper case (the word is in lowercase)
                        tempStr =
                            tempStr.substr(0, 1).toUpperCase() +
                            tempStr.substr(1);
                    }
                    result += tempStr;
                }

                return result;
            }

            // this function call all other functions
            function toCamelCaseString(input) {
                let words = toWords(input);
                return toCamelCase(words);
            }

            return scrape();
        });

        return results;
    }

    async function scrapeReport(page, label) {
        log("selecting date range[" + label + "]");
        // click on date range selector combobox
        await page.click(
            "div.search-results div[aria-label='Incoming Wire Payments Report'] div.dateRange input[role='combobox']"
        );
        await snooze(500);

        // click on last 3 days
        await page.evaluate(label => {
            let selector =
                "div.search-results div[aria-label='Incoming Wire Payments Report'] div.dateRange span.ng-option-label[aria-label='" +
                label +
                "']";
            document.querySelector(selector).click();
        }, label);
        await snooze(500);

        log("clicking on generate");

        // click on generate
        reportPage = null;
        openingReport = true;

        let pages = await browser.pages();

        await page.click(
            "button.bmo-btn-large[aria-label='Generate Incoming Wire Payments Report']"
        );
        await snooze(1000);

        pages = await browser.pages();

        reportPage = pages[pages.length - 1];

        while (!(typeof reportPage.waitForSelector === "function"))
            await snooze(1000);

        await reportPage.setDefaultNavigationTimeout(120000);
        reportPage.setDefaultTimeout(120000);

        await reportPage.waitForSelector("div.report-header-title.row");
        await reportPage.waitForSelector("a[onclick='wireShowAll();']");
        await snooze(4000);

        // pagination
        let totalPages = 1;
        try {
            totalPages = parseInt(
                await reportPage.evaluate(() => {
                    return document
                        .querySelector("[aria-label='Current Page']")
                        .nextSibling.wholeText.replace(/[^0-9]/g, "");
                })
            );
        } catch (error) {}

        log("scrolling to bottom of " + totalPages + " pages");

        if (totalPages > 1) {
            for (let i = 0; i < totalPages; i++) {
                await reportPage.click("button[name='nextPage']");
                await snooze(2000);
            }
        }

        log("scraping -> " + label);
        let result = await scrape(reportPage);

        // close the tab
        await reportPage.close();

        return result;
    }

    let browser = await puppeteer.launch({
        slowMo: 75,
        headless: true,
        args: [
            "--no-sandbox",
            "--disable-setuid-sandbox",
            "--proxy-server=http://" + proxyHost + ":" + proxyPort
        ]
    });

    let page = await browser.newPage();
    await page.authenticate({ username: proxyUser, password: proxyPass });
    await page.setCacheEnabled(false);

    let reportPage = null;

    await page.setDefaultNavigationTimeout(120000);
    page.setDefaultTimeout(120000);
    await page.setViewport({ width: 1280, height: 800 });

    await page.goto("https://www21.bmo.com", { waitUntil: "networkidle0" });
    await page.waitForSelector("form#loginFormID input#customerId");
    log("typing credentials");
    await page.type("input#customerId", conf.customerId);
    await page.type("input#userId", conf.userId);
    await page.type("input#loginPassword", conf.password);

    log("clicking sign-in");

    await page.click("button.sign-in-btn");
    await snooze(10000);

    let continueBtn = await page.evaluate(() => {
        return {
            btn: document.querySelector("button.sign-in-btn"),
            select: document.querySelector("select#sessionOverRide")
        };
    });

    if (continueBtn.select && continueBtn.btn) {
        log("continue button is there");
        await page.click("button.sign-in-btn");
    } else if (!continueBtn.select && continueBtn.btn) {
        process.exit(1);
    } else log("no continue btn found");

    // wait main page to render
    await page.waitForSelector("table.account-balances");

    await snooze(5000);

    let popUp = await page.evaluate(() => {
        return { btn: document.querySelector("button[aria-label='close']") };
    });

    if (popUp.btn) {
        log("popup is there");
        await page.click("button[aria-label='close']");
        await snooze(2000);
    } else log("no popup found");

    log("clicking on wire reports");

    // click on "Payments & Receivables"
    await page.click("li#paymentsMenu > a");

    // wait for rendering
    await page.waitForSelector(
        "div#payment div.popover-main-cont div.menu-item-section div.col-md-3:nth-of-type(1) ul > li:nth-of-type(1)"
    );

    // click on "Wire Payment / Reports"
    await page.click(
        "div#payment div.popover-main-cont div.menu-item-section div.col-md-3:nth-of-type(1) ul > li:nth-of-type(1)"
    );

    // wait for wire payment reports page to render
    await page.waitForSelector("table.manageReportTable");
    await page.waitForSelector(
        "div.search-results div[aria-label='Incoming Wire Payments Report'] div.dateRange input[role='combobox']"
    );

    let resultLastDays = await scrapeReport(page, "Last 3 days");
    let resultToday = await scrapeReport(page, "Current day");

    let results = {
        transactionSummary: resultToday.transactionSummary.concat(
            resultLastDays.transactionSummary
        ),
        transactionDetail: resultToday.transactionDetail.concat(
            resultLastDays.transactionDetail
        )
    };

    await snooze(100);

    await page.click("li.signout-option > a");
    await snooze(3000);
    await page.close();
    await browser.close();

    let resultsStr = JSON.stringify(results);
    log(
        "writing[" +
            results.transactionDetail.length +
            "] to file -> " +
            "data-" +
            conf.sequence +
            ".json"
    );
    console.log(resultsStr);
})().catch(function(error) {
    console.error(error);
    process.exit(1);
});
