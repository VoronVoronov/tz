FROM node:20-alpine

WORKDIR /var/www

RUN apk add --no-cache python3 make g++

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build

USER node

CMD ["npm", "run", "dev"]
