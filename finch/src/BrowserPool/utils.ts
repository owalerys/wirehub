import * as puppeteer from 'puppeteer'
import Debug from '../utils/debug'
import {headless} from '../config'
import * as config from '../config'

const debug = Debug('browser-pool:utils')

export async function launchBrowser (retries: number = 1): Promise<puppeteer.Browser> {
  const launchArgs = {
    executablePath: 'google-chrome-stable',
    headless,
    args: [
      '--disable-dev-shm-usage',
      '--no-sandbox',
      '--disable-setuid-sandbox',
      `--proxy-server=http://${config.proxyHost}:${config.proxyPort}`,
    ],
  }

  try {
    return await puppeteer.launch(launchArgs)
  } catch (err) {
    console.error(err)

    if (retries > 0) {
      debug(`Issue launching Chrome, retrying ${retries} times.`)
      return await launchBrowser(retries - 1)
    }

    debug(`Issue launching Chrome, retries exhausted.`)
    throw err
  }
}
