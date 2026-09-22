## Penyesuaian MHCU v2
- [x] Konfirmasi ke user: 5 poin di bagian 2 (threshold Psikososial, mapping role, batas checkbox instrument 8, fallback profil, teks NB manajemen)
- [x] Jalankan seed-adjustment-v2-mhcu.sql (review dulu bagian ALTER TABLE) — user jalankan manual
- [x] Update Mhcu_scoring: skoring Instrument 7 (pola sama Instrument 5, aspek beda)
- [x] Update Mhcu_scoring: dashboard 8-profil + override item bunuh diri (cek paling pertama)
- [x] Update GET instrument: filter target_role berdasar presensi_role peserta
- [x] Update selesai_sesi: validasi item terjawab role-aware (instrument 5&6 vs 7&8)
- [x] Jalankan seed-adjustment-v2-hasil-skenario.sql, verifikasi output Mhcu_scoring match (Budi=Optimal Wellbeing, Sari=Immediate Professional Follow-up) — user jalankan manual
- [x] Update template_mhcu_view.php + export_pdf: kesimpulan dari 8 profil, seksi psikososial role-aware
- [ ] (Opsional) Tambah 1 skenario uji coba baru role Pimpinan di seed, utk test Instrument 7/8 end-to-end
