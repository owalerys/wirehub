FROM cimg/node:lts-browsers
WORKDIR /app

USER root

RUN wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add -

RUN sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'

RUN apt-get update

RUN apt-get install -y google-chrome-stable

# Add user so we don't need --no-sandbox.
RUN groupadd -r pptruser && useradd -r -g pptruser -G audio,video pptruser \
    && mkdir -p /home/pptruser/Downloads \
    && chown -R pptruser:pptruser /home/pptruser\
    && chown -R pptruser:pptruser /app

# Run everything after as non-privileged user.
USER pptruser

COPY package*.json ./
RUN npm install
COPY . ./
RUN npx tsc

RUN npm prune --production

EXPOSE 3000
CMD [ "node", "./dist/index.js" ]
