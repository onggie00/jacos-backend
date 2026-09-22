import admin from "firebase-admin";
import { Sequelize } from "sequelize";
import dotenv from "dotenv";
import { scheduler } from "./scheduler.js";
import cert from "./labscib-app-b3e6de84549b.json" assert {type: "json"};
import { existsSync, mkdir, mkdirSync, readFileSync, writeFileSync } from "fs";
import moment from "moment";

dotenv.config();

process.env.TZ = "Asia/Jakarta";

export const app = admin.initializeApp({
    credential: admin.credential.cert(cert),
});

export const sequelize = new Sequelize(process.env.DB_NAME, process.env.DB_USERNAME, process.env.DB_PASSWORD, {
    host: process.env.DB_HOST,
    dialect: "mariadb"
});

export const log = (data) => {
    const date = moment().format("YYYY-MM-DD HH:mm:ss");
    const location = `logs/log-${date.split(" ")[0]}.log`;

    if (!existsSync("logs")) mkdirSync("logs");
    if (!existsSync(location)) writeFileSync(location, "");

    const currentLogs = readFileSync(location);
    const newLine = `${date}\t${data}`;
    const newContent = `${currentLogs}\n${newLine}`;

    writeFileSync(location, newContent);
    console.log(newLine);
}

log("Scheduler is starting");
scheduler();

