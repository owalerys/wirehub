import {Pool, createPool} from 'generic-pool'
import {Browser} from 'puppeteer'
import {launchBrowser} from './utils'

export interface IBrowserPoolOptions {
  maxPoolSize: number,
  minPoolSize: number,
  acquisitionTimeout: number,
  connectionTimeout: number
}

const borrowedResources: Map<Browser, Date> = new Map()
export default class BrowserPool {
  static factory = {
    create () {
      return launchBrowser()
    },
    destroy (browser: Browser): Promise<void> {
      return browser.close()
    }
  }

  private pool: Pool<Browser>
  private connectionTimeout?: number
  private acquisitionTimeout?: number

  constructor (options: IBrowserPoolOptions) {
    const maxPoolSize = options.maxPoolSize
    const minPoolSize = options.minPoolSize
    const max = Math.max(maxPoolSize, 1)
    if (max > 10) {
      process.setMaxListeners(max);
    }
    const min = Math.max(minPoolSize, 1)

    this.connectionTimeout = options.connectionTimeout
    this.acquisitionTimeout = options.acquisitionTimeout
    setInterval(this.timeoutCheck.bind(this), this.acquisitionTimeout)

    this.pool = createPool<Browser>(BrowserPool.factory, {
      max,
      min,
      acquireTimeoutMillis: this.acquisitionTimeout
    })
  }

  async acquire (): Promise<Browser> {
    const browser = await this.pool.acquire()
    borrowedResources.set(browser, new Date())
    return browser
  }

  async release (browser: Browser): Promise<void> {
    await this.pool.release(browser)
    borrowedResources.delete(browser)
  }

  async destroy (browser: Browser): Promise<void> {
    await this.pool.destroy(browser)
    borrowedResources.delete(browser)
  }

  async timeoutCheck (): Promise<void> {
    if (!this.connectionTimeout) {
      return
    }

    const now = Date.now()
    for (const [browser, createdAt] of borrowedResources.entries()) {
      if (now - createdAt.valueOf() > this.connectionTimeout) {
        console.error('Possible browser leak detected')
        try {
          await this.pool.destroy(browser)
        } catch (_) {
        }
        borrowedResources.delete(browser)
      }
    }
  }
}
