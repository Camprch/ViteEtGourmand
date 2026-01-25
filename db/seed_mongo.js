// Seed MongoDB orders_stats for charts (mongosh script)
// Usage:
//   mongosh "mongodb://USER:PASS@localhost:27017/admin" db/seed_mongo.js
// Optional env vars:
//   MONGO_DB (default: vite_gourmand)
//   COUNT (default: 240)
//   DAYS (default: 180)

const dbName = process.env.MONGO_DB || "vite_gourmand";
const count = parseInt(process.env.COUNT || "240", 10);
const days = parseInt(process.env.DAYS || "180", 10);

const menus = [
  { id: 1, titre: "Salade végé", prix_min: 19.5, prix_max: 26.0, weight: 0.12 },
  { id: 2, titre: "Boeuf bourguignon", prix_min: 26.9, prix_max: 34.0, weight: 0.25 },
  { id: 3, titre: "Choucroute", prix_min: 25.9, prix_max: 33.0, weight: 0.18 },
  { id: 4, titre: "Poulet rôti", prix_min: 23.9, prix_max: 31.0, weight: 0.45 },
];

function pickMenu() {
  const r = Math.random();
  let acc = 0;
  for (const m of menus) {
    acc += m.weight;
    if (r <= acc) return m;
  }
  return menus[menus.length - 1];
}

function randBetween(min, max) {
  return min + Math.random() * (max - min);
}

function randomDateInLastDays(maxDays) {
  const now = new Date();
  const past = new Date(now.getTime() - maxDays * 24 * 60 * 60 * 1000);
  const ts = past.getTime() + Math.random() * (now.getTime() - past.getTime());
  return new Date(ts);
}

const dbRef = db.getSiblingDB(dbName);
const col = dbRef.orders_stats;

// Clear existing data to avoid duplicates when reseeding
col.deleteMany({});

const bulk = [];
for (let i = 0; i < count; i++) {
  const menu = pickMenu();
  const nbPersonnes = 2 + Math.floor(Math.random() * 8);
  const prixTotal = Number((randBetween(menu.prix_min, menu.prix_max) * nbPersonnes).toFixed(2));
  const acceptedAt = randomDateInLastDays(days);

  bulk.push({
    insertOne: {
      document: {
        commande_id: 100000 + i,
        menu_id: menu.id,
        menu_titre: menu.titre,
        prix_total: prixTotal,
        accepted_at: acceptedAt,
      },
    },
  });
}

if (bulk.length) {
  col.bulkWrite(bulk);
}

print(`Inserted ${bulk.length} docs into ${dbName}.orders_stats`);
