import { rejects } from "assert";
import moment from "moment";
import { app, log, sequelize } from "./index.js";

export const scheduler = () => {
    const firebaseMessaging = app.messaging();
    const degrees = ["sd", "smp", "sma", "ft"];

    // Timers
    var eventSchedulerTimer, ePaymentSchedulerTimer;

    const eventScheduler = async () => {
        log("[EVENT] Scheduler fired");
        clearInterval(eventSchedulerTimer);

        let deviceIds = [];
        let userData = [];
        const date = new Date(moment().format("YYYY-MM-DD HH:mm:ss"));

        for (const i in degrees) {
            const degree = degrees[i];
            const siswa = await sequelize.query(`SELECT id_siswa_${degree}, device_id_siswa, device_id_ortu FROM siswa_${degree}_aktif`);

            siswa[0].forEach(e => {
                e.degree = degree;
                e.role = "siswa";
                e.id = e[`id_siswa_${degree}`];

                userData.push(e);
                deviceIds.push(e.device_id_siswa, e.device_id_ortu);
            });

            const guru = await sequelize.query(`SELECT * FROM guru_${degree}`);

            guru[0].forEach(e => {
                e.degree = degree;
                e.role = "guru";
                e.id = e.id_guru;

                userData.push(e);
                deviceIds.push(e.device_id);
            });

            if (degree == "ft") continue;
            
            const pimpinan = await sequelize.query(`SELECT * FROM pimpinan_${degree}`);

            pimpinan[0].forEach(e => {
                e.degree = degree;
                e.role = "pimpinan";
                e.id = e.id_pimpinan;

                userData.push(e);
                deviceIds.push(e.device_id);
            });
        }

        const pimpinan = await sequelize.query(`SELECT * FROM pegawai`);

        pimpinan[0].forEach(e => {
            e.degree = "";
            e.role = "pegawai";
            e.id = e.id_pegawai;

            userData.push(e);
            deviceIds.push(e.device_id);
        });

        deviceIds = deviceIds.filter((e) => e);

        if (deviceIds.length < 1) {
            return log("[EVENT] All devices are logged out. No further actions are being performed.");
        }

        // Events scheduler
        if (date.getHours() == 0 && date.getMinutes() == 0) {
            const eventsQuery = `SELECT * FROM kalender_akademik WHERE date < '${moment(date).add({ day: 1 }).format("YYYY-MM-DD")}' AND date >= '${moment(date).format("YYYY-MM-DD")}'`;
            const events = await sequelize.query(eventsQuery);

            for (const i in events[0]) {
                const event = events[0][i];

                try {
                    log(`[EVENT] Notifications are being sent to ${deviceIds.length} devices`);

                    const data = {
                        "title": "Event Labschool",
                        "content": `Hari ini adalah ${event.label}. Klik untuk selengkapnya`,
                        "action": `event.${event.id_kalender}`,
                        "created_at": moment(date).format("YYYY-MM-DD HH:mm:ss")
                    };

                    const send = await firebaseMessaging.sendToDevice(
                        deviceIds,
                        {
                            notification: {
                                title: data.title,
                                body: data.content
                            },
                            data: data
                        }
                    );

                    log("[EVENT] Notifications request has been sent. Determining whether it was a success or not");
                    log("[EVENT] Inserting notifications data into database");

                    for (const o in userData) {
                        const user = userData[o];
                        const notificationData = {
                            ...data,
                            role: "siswa",
                            jenjang: user.degree,
                            user_id: user.id,
                        };

                        await sequelize.query(`INSERT INTO list_notifikasi (title, content, role, jenjang, user_id, action) VALUES ('${notificationData.title}', '${notificationData.content}', '${notificationData.role}', '${notificationData.jenjang}', '${notificationData.user_id}', '${notificationData.action}')`);
                    }

                    if (send.successCount < 1) throw send;

                    log(`[EVENT] Successfully sent ${deviceIds.length} notifications`)
                }
                catch (e) {
                    log("[EVENT] Notifications were not successfully delivered to users");
                }
            }
        }

        // Agenda scheduler
        const agendaQuery = `SELECT * FROM agenda WHERE created_at >= '${moment(date).subtract({ minute: 2 }).format("YYYY-MM-DD HH:mm:ss")}' AND created_at <= '${moment(date).format("YYYY-MM-DD HH:mm:ss")}'`;
        const agendas = await sequelize.query(agendaQuery);

        for (const i in agendas[0]) {
            const agenda = agendas[0][i];

            try {
                log(`[AGENDA] Notifications are being sent to ${deviceIds.length} devices`);

                const data = {
                    "title": "Agenda Labschool",
                    "content": `Hari ini adalah ${agenda.judul}. Klik untuk selengkapnya`,
                    "action": `agenda.${agenda.id_agenda}`,
                    "created_at": moment(date).format("YYYY-MM-DD HH:mm:ss")
                };

                const send = await firebaseMessaging.sendToDevice(
                    deviceIds,
                    {
                        notification: {
                            title: data.title,
                            body: data.content,
                            icon: agenda.img_thumbnail
                        },
                        data: data
                    }
                );

                log("[AGENDA] Notifications request has been sent. Determining whether it was a success or not");
                log("[AGENDA] Inserting notifications data into database");

                for (const o in userData) {
                    const user = userData[o];
                    const notificationData = {
                        ...data,
                        role: user.role,
                        jenjang: user.degree,
                        user_id: user.id,
                    };

                    await sequelize.query(`INSERT INTO list_notifikasi (title, content, role, jenjang, user_id, action) VALUES ('${notificationData.title}', '${notificationData.content}', '${notificationData.role}', '${notificationData.jenjang}', '${notificationData.user_id}', '${notificationData.action}')`);
                }

                if (send.successCount < 1) throw send;

                log(`[AGENDA] Successfully sent ${deviceIds.length} notifications`)
            }
            catch (e) {
                log("[AGENDA] Notifications were not successfully delivered to users");
            }
        }

        log("[AGENDA] Scheduler completed")

        eventSchedulerTimer = setInterval(eventScheduler, 60000);
    }

    const ePaymentScheduler = async () => {
        log("[E-PAYMENT] Scheduler fired");

        const date = new Date(moment().format("YYYY-MM-DD HH:mm:ss"));
        const georgianDate = date.getDate();
        
        if ([1, 11].indexOf(georgianDate) < 0 || (georgianDate < 11 && georgianDate != 1)) return;

        log("[E-PAYMENT] Scanning students for each degrees");

        for (const i in degrees) {
            const degree = degrees[i];
            const dataSiswa = await sequelize.query(`SELECT id_tahun_ajaran, id_siswa_${degree}, id_siswa_${degree}_aktif, device_id_siswa, device_id_ortu FROM siswa_${degree}_aktif`);

            log(`[E-PAYMENT] Scanning students from ${degree} degree`);

            for (const o in dataSiswa[0]) {
                const siswa = dataSiswa[0][o];
                const dataSpp = await sequelize.query(`SELECT * FROM transaksi_spp WHERE deadline < '${moment(date).format("YYYY-MM-DD")}' AND id_siswa_aktif=${siswa[`id_siswa_${degree}_aktif`]} AND no_transaksi LIKE '%${degree.toUpperCase()}%'`);

                log(`[E-PAYMENT] ${dataSpp[0].length} transactions found for student id#${siswa[`id_siswa_${degree}_aktif`]}`);

                for (const x in dataSpp[0]) {
                    const spp = dataSpp[0][x];

                    if (spp.status_transaksi == 2) continue;

                    try {
                        const data = {
                            "title": spp.description,
                            "content": `Anda memiliki tagihan yang harus dibayarkan`,
                            "action": `e_payment.${spp.id_transaksi}`,
                            "created_at": moment(date).format("YYYY-MM-DD HH:mm:ss")
                        };
                        const send = await firebaseMessaging.sendToDevice(
                            [siswa.device_id_ortu].filter(e => e),
                            {
                                notification: {
                                    title: data.title,
                                    body: data.content
                                },
                                data: data
                            }
                        );

                        if (send.successCount < 1) throw send;

                        const notificationData = {
                            ...data,
                            role: "siswa",
                            jenjang: degree,
                            user_id: siswa[`id_siswa_${degree}`],
                        };

                        await sequelize.query(`INSERT INTO list_notifikasi (title, content, role, jenjang, user_id, action) VALUES ('${notificationData.title}', '${notificationData.content}', '${notificationData.role}', '${notificationData.jenjang}', '${notificationData.user_id}', '${notificationData.action}')`);
                    } catch (e) {
                        console.log(e);
                        log("[E-PAYMENT] Notification was not sent");
                    }
                }
            }
        }
    }

    // Initiate schedulers
    eventSchedulerTimer = setInterval(eventScheduler, 60000);
    ePaymentSchedulerTimer = setInterval(ePaymentScheduler, 12 * 60 * 60 * 1000);

    log("Scheduler is running in the background");
}