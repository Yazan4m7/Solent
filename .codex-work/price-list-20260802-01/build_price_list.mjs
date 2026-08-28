import fs from "node:fs/promises";
import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = "C:/Users/Yazan/Desktop/Projects/Solent/app/outputs/solent-price-list-20260802";
await fs.mkdir(outputDir, { recursive: true });

const workbook = Workbook.create();
const pricing = workbook.worksheets.add("Pricing & Plans");
const sales = workbook.worksheets.add("Sales Guide");

const colors = {
  navy: "#0B1F33",
  blue: "#155E75",
  teal: "#0F766E",
  tealLight: "#DDF4EF",
  blueLight: "#E8F2F8",
  slate: "#334155",
  muted: "#64748B",
  line: "#CBD5E1",
  soft: "#F8FAFC",
  white: "#FFFFFF",
  amber: "#B45309",
  amberLight: "#FFF7E6",
};

function applyTitle(sheet, title, subtitle, lastColumn) {
  sheet.showGridLines = false;
  sheet.getRange(`A1:${lastColumn}1`).merge();
  sheet.getRange("A1").values = [[title]];
  sheet.getRange(`A1:${lastColumn}1`).format = {
    fill: colors.navy,
    font: { bold: true, color: colors.white, size: 20 },
    verticalAlignment: "center",
    horizontalAlignment: "left",
  };
  sheet.getRange(`A1:${lastColumn}1`).format.rowHeight = 38;

  sheet.getRange(`A2:${lastColumn}2`).merge();
  sheet.getRange("A2").values = [[subtitle]];
  sheet.getRange(`A2:${lastColumn}2`).format = {
    fill: colors.blueLight,
    font: { color: colors.slate, size: 10, italic: true },
    verticalAlignment: "center",
    horizontalAlignment: "left",
  };
  sheet.getRange(`A2:${lastColumn}2`).format.rowHeight = 24;
}

function applySectionHeader(sheet, rangeAddress, label) {
  const range = sheet.getRange(rangeAddress);
  range.merge();
  range.values = [[label]];
  range.format = {
    fill: colors.blue,
    font: { bold: true, color: colors.white, size: 11 },
    verticalAlignment: "center",
    horizontalAlignment: "left",
  };
  range.format.rowHeight = 25;
}

applyTitle(
  pricing,
  "SOLENT INTERNAL PRICE LIST",
  "Internal use only  |  All prices in JOD  |  Pricing is per laboratory, not per employee or user",
  "G",
);

applySectionHeader(pricing, "A4:G4", "APPROVED PRICING");

pricing.getRange("A5:G5").values = [[
  "Plan",
  "Best For",
  "Monthly",
  "Annual",
  "Annual Savings",
  "Permanent License",
  "Maintenance / Year",
]];
pricing.getRange("A5:G5").format = {
  fill: colors.slate,
  font: { bold: true, color: colors.white, size: 10 },
  horizontalAlignment: "center",
  verticalAlignment: "center",
  wrapText: true,
  borders: { preset: "outside", style: "thin", color: colors.slate },
};
pricing.getRange("A5:G5").format.rowHeight = 32;

pricing.getRange("A6:G8").values = [
  ["Standard", "Smaller laboratories needing the essential workflow", 50, 500, null, 1000, 150],
  ["Plus — Recommended", "Established laboratories needing the complete daily workflow", 70, 700, null, 1500, 200],
  ["Premium", "Larger or more complex laboratories requiring additional assistance", 100, 1000, null, 2200, 300],
];
pricing.getRange("E6").formulas = [["=C6*12-D6"]];
pricing.getRange("E6:E8").fillDown();
pricing.getRange("C6:G8").format.numberFormat = '"JOD" #,##0';
pricing.getRange("A6:G8").format = {
  font: { color: colors.slate, size: 10 },
  verticalAlignment: "center",
  borders: {
    insideHorizontal: { style: "thin", color: colors.line },
    bottom: { style: "thin", color: colors.line },
  },
};
pricing.getRange("A6:B8").format.horizontalAlignment = "left";
pricing.getRange("C6:G8").format.horizontalAlignment = "right";
pricing.getRange("A6:G6").format.fill = colors.soft;
pricing.getRange("A7:G7").format.fill = colors.tealLight;
pricing.getRange("A7:G7").format.font = { bold: true, color: colors.teal, size: 10 };
pricing.getRange("A8:G8").format.fill = colors.soft;
pricing.getRange("A6:G8").format.rowHeight = 38;

pricing.getRange("A10:G10").merge();
pricing.getRange("A10").values = [["Annual pricing equals ten monthly payments — two months free."]];
pricing.getRange("A10:G10").format = {
  fill: colors.amberLight,
  font: { bold: true, color: colors.amber, size: 10 },
  verticalAlignment: "center",
  horizontalAlignment: "left",
  borders: { preset: "outside", style: "thin", color: "#F4C78D" },
};
pricing.getRange("A10:G10").format.rowHeight = 26;

applySectionHeader(pricing, "A12:G12", "PLAN COVERAGE");
pricing.getRange("A13:G13").values = [["Plan", "Core Coverage", null, null, null, "Service & Positioning", null]];
pricing.getRange("B13:E13").merge();
pricing.getRange("F13:G13").merge();
pricing.getRange("A13:G13").format = {
  fill: colors.slate,
  font: { bold: true, color: colors.white, size: 10 },
  horizontalAlignment: "left",
  verticalAlignment: "center",
  borders: { preset: "outside", style: "thin", color: colors.slate },
};
pricing.getRange("A13:G13").format.rowHeight = 26;

const coverageRows = [
  {
    row: 14,
    plan: "Standard",
    coverage: "• Case and Job management\n• Status, Waiting and assignment tracking\n• Dashboard and essential reports",
    service: "Standard remote support\nEntry-level plan",
    fill: colors.soft,
  },
  {
    row: 15,
    plan: "Plus — Recommended",
    coverage: "• Everything in Standard\n• Materials and production management\n• Delivery scheduling\n• Advanced reports and exports",
    service: "Remote onboarding and training\nRecommended for most laboratories",
    fill: colors.tealLight,
  },
  {
    row: 16,
    plan: "Premium",
    coverage: "• Everything in Plus\n• Assisted data import\n• Advanced onboarding and workflow configuration",
    service: "Priority support\nCustom requirements quoted separately",
    fill: colors.soft,
  },
];

for (const item of coverageRows) {
  pricing.getRange(`B${item.row}:E${item.row}`).merge();
  pricing.getRange(`F${item.row}:G${item.row}`).merge();
  pricing.getRange(`A${item.row}`).values = [[item.plan]];
  pricing.getRange(`B${item.row}`).values = [[item.coverage]];
  pricing.getRange(`F${item.row}`).values = [[item.service]];
  pricing.getRange(`A${item.row}:G${item.row}`).format = {
    fill: item.fill,
    font: { color: colors.slate, size: 10, bold: item.row === 15 },
    verticalAlignment: "center",
    horizontalAlignment: "left",
    wrapText: true,
    borders: { bottom: { style: "thin", color: colors.line } },
  };
  pricing.getRange(`A${item.row}:G${item.row}`).format.rowHeight = item.row === 15 ? 76 : 64;
}

pricing.getRange("A18:G18").merge();
pricing.getRange("A18").values = [["Permanent License means permanent use of the installed version; it does not transfer source-code ownership or resale rights."]];
pricing.getRange("A18:G18").format = {
  fill: colors.blueLight,
  font: { color: colors.slate, size: 9, italic: true },
  verticalAlignment: "center",
  horizontalAlignment: "left",
  wrapText: true,
  borders: { preset: "outside", style: "thin", color: colors.line },
};
pricing.getRange("A18:G18").format.rowHeight = 34;

pricing.getRange("A1:A18").format.columnWidth = 22;
pricing.getRange("B1:B18").format.columnWidth = 48;
pricing.getRange("C1:D18").format.columnWidth = 15;
pricing.getRange("E1:E18").format.columnWidth = 18;
pricing.getRange("F1:F18").format.columnWidth = 21;
pricing.getRange("G1:G18").format.columnWidth = 21;
pricing.freezePanes.freezeRows(5);

applyTitle(
  sales,
  "SOLENT SALES GUIDE",
  "Quick commercial rules for consistent customer quotations",
  "F",
);

applySectionHeader(sales, "A4:F4", "QUICK REFERENCE");
const quickReference = [
  ["Default recommendation", "Plus — 70 JOD monthly / 700 JOD annually / 1,500 JOD permanent license"],
  ["Trial", "14-day Plus trial"],
  ["Annual offer", "Two months free compared with monthly billing"],
  ["Pricing basis", "Per laboratory; never based on employee or user count"],
  ["Discounts", "Any additional discount requires owner approval"],
  ["Custom work", "Custom development, on-site visits and complex migration are quoted separately"],
];

quickReference.forEach((entry, index) => {
  const row = 5 + index;
  sales.getRange(`B${row}:F${row}`).merge();
  sales.getRange(`A${row}`).values = [[entry[0]]];
  sales.getRange(`B${row}`).values = [[entry[1]]];
  sales.getRange(`A${row}`).format = {
    fill: index === 0 ? colors.tealLight : colors.soft,
    font: { bold: true, color: index === 0 ? colors.teal : colors.slate, size: 10 },
    verticalAlignment: "center",
    horizontalAlignment: "left",
  };
  sales.getRange(`B${row}:F${row}`).format = {
    fill: index === 0 ? colors.tealLight : colors.soft,
    font: { color: colors.slate, size: 10, bold: index === 0 },
    verticalAlignment: "center",
    horizontalAlignment: "left",
    wrapText: true,
    borders: { bottom: { style: "thin", color: colors.line } },
  };
  sales.getRange(`A${row}:F${row}`).format.rowHeight = row === 5 || row === 10 ? 34 : 28;
});

applySectionHeader(sales, "A12:F12", "SUBSCRIPTION TERMS");
const subscriptionTerms = [
  ["Updates & support", "Included during an active subscription"],
  ["Hosting", "Included when using a Solent-managed server"],
  ["Billing", "Monthly or annual payment"],
  ["Annual value", "Charged at ten monthly payments"],
];
subscriptionTerms.forEach((entry, index) => {
  const row = 13 + index;
  sales.getRange(`B${row}:F${row}`).merge();
  sales.getRange(`A${row}`).values = [[entry[0]]];
  sales.getRange(`B${row}`).values = [[entry[1]]];
});

applySectionHeader(sales, "A18:F18", "PERMANENT LICENSE TERMS");
const permanentTerms = [
  ["License scope", "One laboratory and one production installation"],
  ["Included", "Installation, training, updates and support for 12 months"],
  ["After 12 months", "The installed version continues working permanently"],
  ["Maintenance", "Optional annual maintenance at the applicable tier rate"],
  ["Client server", "VPS, hosting, backup and server-management costs are not included"],
  ["Ownership", "No source-code ownership, resale rights or intellectual-property transfer"],
];
permanentTerms.forEach((entry, index) => {
  const row = 19 + index;
  sales.getRange(`B${row}:F${row}`).merge();
  sales.getRange(`A${row}`).values = [[entry[0]]];
  sales.getRange(`B${row}`).values = [[entry[1]]];
});

applySectionHeader(sales, "A26:F26", "SALES RULES");
const salesRules = [
  ["1", "Recommend Plus unless the customer’s requirements clearly match another plan."],
  ["2", "Select the plan according to required features and service—not perceived ability to pay."],
  ["3", "Do not promise discounts, custom features or delivery dates without owner approval."],
  ["4", "Explain that a Permanent License is a usage license, not ownership of the software or source code."],
];
salesRules.forEach((entry, index) => {
  const row = 27 + index;
  sales.getRange(`B${row}:F${row}`).merge();
  sales.getRange(`A${row}`).values = [[entry[0]]];
  sales.getRange(`B${row}`).values = [[entry[1]]];
});

applySectionHeader(sales, "A32:F32", "QUICK SALES WORDING");
sales.getRange("A33:F35").merge();
sales.getRange("A33").values = [[
  "Our plans start from 50 JOD per month. The most popular option is Plus at 70 JOD monthly or 700 JOD annually. Permanent licenses are also available from 1,000 JOD. We recommend the best plan based on the laboratory’s required workflow and support level.",
]];
sales.getRange("A33:F35").format = {
  fill: colors.tealLight,
  font: { color: colors.slate, size: 11, italic: true },
  verticalAlignment: "center",
  horizontalAlignment: "left",
  wrapText: true,
  borders: { preset: "outside", style: "thin", color: colors.teal },
};

for (const range of ["A13:F16", "A19:F24", "A27:F30"]) {
  sales.getRange(range).format = {
    fill: colors.soft,
    font: { color: colors.slate, size: 10 },
    verticalAlignment: "center",
    horizontalAlignment: "left",
    wrapText: true,
    borders: { insideHorizontal: { style: "thin", color: colors.line } },
  };
}
sales.getRange("A13:A16").format.font = { bold: true, color: colors.slate, size: 10 };
sales.getRange("A19:A24").format.font = { bold: true, color: colors.slate, size: 10 };
sales.getRange("A27:A30").format = {
  fill: colors.tealLight,
  font: { bold: true, color: colors.teal, size: 11 },
  horizontalAlignment: "center",
  verticalAlignment: "center",
};
sales.getRange("A13:F16").format.rowHeight = 28;
sales.getRange("A19:F24").format.rowHeight = 30;
sales.getRange("A27:F30").format.rowHeight = 34;

sales.getRange("A1:A35").format.columnWidth = 24;
sales.getRange("B1:F35").format.columnWidth = 18;
sales.freezePanes.freezeRows(4);

const pricingCheck = await workbook.inspect({
  kind: "table",
  range: "'Pricing & Plans'!A1:G18",
  include: "values,formulas",
  tableMaxRows: 20,
  tableMaxCols: 8,
});
console.log("PRICING_CHECK");
console.log(pricingCheck.ndjson);

const salesCheck = await workbook.inspect({
  kind: "table",
  range: "'Sales Guide'!A1:F35",
  include: "values,formulas",
  tableMaxRows: 40,
  tableMaxCols: 7,
});
console.log("SALES_CHECK");
console.log(salesCheck.ndjson);

const errors = await workbook.inspect({
  kind: "match",
  searchTerm: "#REF!|#DIV/0!|#VALUE!|#NAME\\?|#N/A",
  options: { useRegex: true, maxResults: 100 },
  summary: "final formula error scan",
});
console.log("FORMULA_ERROR_SCAN");
console.log(errors.ndjson);

for (const [sheetName, fileName, range] of [
  ["Pricing & Plans", "pricing-preview.png", "A1:G18"],
  ["Sales Guide", "sales-guide-preview.png", "A1:F35"],
]) {
  const preview = await workbook.render({ sheetName, range, scale: 1.4, format: "png" });
  await fs.writeFile(`${outputDir}/${fileName}`, new Uint8Array(await preview.arrayBuffer()));
}

const output = await SpreadsheetFile.exportXlsx(workbook);
await output.save(`${outputDir}/Solent_Internal_Price_List.xlsx`);
console.log(`OUTPUT=${outputDir}/Solent_Internal_Price_List.xlsx`);
