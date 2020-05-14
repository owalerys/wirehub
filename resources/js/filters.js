import numeral from "numeral";
import Vue from "vue";

Vue.filter("money", val => {
    return numeral(val).format("$0,0.00");
});
