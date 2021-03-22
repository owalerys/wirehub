import * as _debug from 'debug'

_debug.enable("*");

export default function Debug (namespace) {
  return _debug(`puppeteer-as-a-service:${namespace}`)
}
