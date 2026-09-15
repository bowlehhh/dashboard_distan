---
paths:
  - 'database/seeders/**'
---

# Seeders

## Seeder produksi tidak boleh mengisi data dummy
DatabaseSeeder hanya memastikan akun admin awal tersedia. Jangan seed Poktan, Alsintan, Saprodi, atau Tanaman Pangan dari DatabaseSeeder, dan jangan menimpa password akun admin yang sudah ada saat deploy berulang.
