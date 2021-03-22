const {env} = process

export const port = getNumber(env.PORT, 3000)
export const maxPoolSize = getNumber(env.MAX_POOL_SIZE, 3)
export const minPoolSize = getNumber(env.MIN_POOL_SIZE, 1)
export const connectionTimeout = getNumber(env.CONNECTION_TIMEOUT, 60000 * 4)
export const healthCheckEndpoint = env.HEALTH_CHECK_ENDPOINT
export const headless = env.HEADLESS !== 'false'
export const proxyHost = env.PROXY_HOST
export const proxyPort = env.PROXY_PORT

function getNumber (value: any, defaults: number): number {
  return (typeof value === 'undefined' ? defaults : Number(value)) || defaults
}
