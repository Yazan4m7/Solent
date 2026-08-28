import fs from "node:fs/promises";
import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = "C:/Users/Yazan/Desktop/Projects/Solent/app/outputs/solent-price-list-20260802";
await fs.mkdir(outputDir, { recursive: true });

const workbook = Workbook.create();
const pricing = workbook.worksheets.add("الأسعار والباقات");
const sales = workbook.worksheets.add("دليل المبيعات");

const colors = {
  navy: "#0B1F33",
  blue: "#155E75",
  teal: "#0F766E",
  tealLight: "#DDF4EF",
  blueLight: "#E8F2F8",
  slate: "#334155",
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
    font: { name: "Arial", bold: true, color: colors.white, size: 20 },
    verticalAlignment: "center",
    horizontalAlignment: "center",
  };
  sheet.getRange(`A1:${lastColumn}1`).format.rowHeight = 38;

  sheet.getRange(`A2:${lastColumn}2`).merge();
  sheet.getRange("A2").values = [[subtitle]];
  sheet.getRange(`A2:${lastColumn}2`).format = {
    fill: colors.blueLight,
    font: { name: "Arial", color: colors.slate, size: 10, italic: true },
    verticalAlignment: "center",
    horizontalAlignment: "center",
  };
  sheet.getRange(`A2:${lastColumn}2`).format.rowHeight = 24;
}

function applySectionHeader(sheet, rangeAddress, label) {
  const range = sheet.getRange(rangeAddress);
  range.merge();
  range.values = [[label]];
  range.format = {
    fill: colors.blue,
    font: { name: "Arial", bold: true, color: colors.white, size: 11 },
    verticalAlignment: "center",
    horizontalAlignment: "center",
  };
  range.format.rowHeight = 25;
}

applyTitle(
  pricing,
  "قائمة الأسعار الداخلية — سولنت",
  "للاستخدام الداخلي فقط  |  جميع الأسعار بالدينار الأردني  |  التسعير لكل مختبر وليس حسب عدد الموظفين أو المستخدمين",
  "G",
);

applySectionHeader(pricing, "A4:G4", "الأسعار المعتمدة");

pricing.getRange("A5:G5").values = [[
  "الصيانة السنوية",
  "الرخصة الدائمة",
  "التوفير السنوي",
  "الاشتراك السنوي",
  "الاشتراك الشهري",
  "الأنسب لـ",
  "الباقة",
]];
pricing.getRange("A5:G5").format = {
  fill: colors.slate,
  font: { name: "Arial", bold: true, color: colors.white, size: 10 },
  horizontalAlignment: "center",
  verticalAlignment: "center",
  wrapText: true,
  borders: { preset: "outside", style: "thin", color: colors.slate },
};
pricing.getRange("A5:G5").format.rowHeight = 32;

pricing.getRange("A6:G8").values = [
  [150, 1000, null, 500, 50, "المختبرات الصغيرة التي تحتاج إلى مسار العمل الأساسي", "Standard"],
  [200, 1500, null, 700, 70, "المختبرات المستقرة التي تحتاج إلى إدارة العمل اليومي بشكل متكامل", "الموصى بها — Plus"],
  [300, 2200, null, 1000, 100, "المختبرات الكبيرة أو ذات المتطلبات الأكثر تعقيداً", "Premium"],
];
pricing.getRange("C6").formulas = [["=E6*12-D6"]];
pricing.getRange("C6:C8").fillDown();
pricing.getRange("A6:E8").format.numberFormat = '#,##0 "د.أ"';
pricing.getRange("A6:G8").format = {
  font: { name: "Arial", color: colors.slate, size: 10 },
  verticalAlignment: "center",
  borders: {
    insideHorizontal: { style: "thin", color: colors.line },
    bottom: { style: "thin", color: colors.line },
  },
};
pricing.getRange("A6:E8").format.horizontalAlignment = "center";
pricing.getRange("F6:G8").format.horizontalAlignment = "right";
pricing.getRange("F6:G8").format.wrapText = true;
pricing.getRange("A6:G6").format.fill = colors.soft;
pricing.getRange("A7:G7").format.fill = colors.tealLight;
pricing.getRange("A7:G7").format.font = { name: "Arial", bold: true, color: colors.teal, size: 10 };
pricing.getRange("A8:G8").format.fill = colors.soft;
pricing.getRange("A6:G8").format.rowHeight = 44;

pricing.getRange("A10:G10").merge();
pricing.getRange("A10").values = [["سعر الاشتراك السنوي يعادل عشرة أشهر — شهران مجاناً."]];
pricing.getRange("A10:G10").format = {
  fill: colors.amberLight,
  font: { name: "Arial", bold: true, color: colors.amber, size: 10 },
  verticalAlignment: "center",
  horizontalAlignment: "right",
  borders: { preset: "outside", style: "thin", color: "#F4C78D" },
};
pricing.getRange("A10:G10").format.rowHeight = 26;

applySectionHeader(pricing, "A12:G12", "محتويات الباقات");
pricing.getRange("A13:G13").values = [["الخدمة والتوصية", null, "الخصائص المشمولة", null, null, null, "الباقة"]];
pricing.getRange("A13:B13").merge();
pricing.getRange("C13:F13").merge();
pricing.getRange("A13:G13").format = {
  fill: colors.slate,
  font: { name: "Arial", bold: true, color: colors.white, size: 10 },
  horizontalAlignment: "right",
  verticalAlignment: "center",
  borders: { preset: "outside", style: "thin", color: colors.slate },
};
pricing.getRange("A13:G13").format.rowHeight = 26;

const coverageRows = [
  {
    row: 14,
    plan: "Standard",
    coverage: "• إدارة الحالات (Job)\n• متابعة الحالة والانتظار والتعيين\n• لوحة التحكم والتقارير الأساسية",
    service: "دعم فني عن بُعد\nالباقة الأساسية",
    fill: colors.soft,
  },
  {
    row: 15,
    plan: "الموصى بها — Plus",
    coverage: "• جميع خصائص Standard\n• إدارة الإنتاج وMaterials\n• جدولة التسليم\n• تقارير متقدمة وتصدير البيانات",
    service: "تهيئة وتدريب عن بُعد\nالموصى بها لمعظم المختبرات",
    fill: colors.tealLight,
  },
  {
    row: 16,
    plan: "Premium",
    coverage: "• جميع خصائص Plus\n• المساعدة في نقل البيانات\n• تهيئة متقدمة وضبط مسار العمل",
    service: "دعم ذو أولوية\nالمتطلبات الخاصة تُسعّر منفصلة",
    fill: colors.soft,
  },
];

for (const item of coverageRows) {
  pricing.getRange(`A${item.row}:B${item.row}`).merge();
  pricing.getRange(`C${item.row}:F${item.row}`).merge();
  pricing.getRange(`A${item.row}`).values = [[item.service]];
  pricing.getRange(`C${item.row}`).values = [[item.coverage]];
  pricing.getRange(`G${item.row}`).values = [[item.plan]];
  pricing.getRange(`A${item.row}:G${item.row}`).format = {
    fill: item.fill,
    font: { name: "Arial", color: colors.slate, size: 10, bold: item.row === 15 },
    verticalAlignment: "center",
    horizontalAlignment: "right",
    wrapText: true,
    borders: { bottom: { style: "thin", color: colors.line } },
  };
  pricing.getRange(`A${item.row}:G${item.row}`).format.rowHeight = item.row === 15 ? 78 : 66;
}

pricing.getRange("A18:G18").merge();
pricing.getRange("A18").values = [["الرخصة الدائمة تعني الاستخدام الدائم للنسخة المثبتة، ولا تنقل ملكية الكود أو حقوق إعادة البيع."]];
pricing.getRange("A18:G18").format = {
  fill: colors.blueLight,
  font: { name: "Arial", color: colors.slate, size: 9, italic: true },
  verticalAlignment: "center",
  horizontalAlignment: "right",
  wrapText: true,
  borders: { preset: "outside", style: "thin", color: colors.line },
};
pricing.getRange("A18:G18").format.rowHeight = 34;

pricing.getRange("A1:A18").format.columnWidth = 20;
pricing.getRange("B1:B18").format.columnWidth = 20;
pricing.getRange("C1:C18").format.columnWidth = 17;
pricing.getRange("D1:E18").format.columnWidth = 16;
pricing.getRange("F1:F18").format.columnWidth = 48;
pricing.getRange("G1:G18").format.columnWidth = 28;
pricing.freezePanes.freezeRows(5);

applyTitle(
  sales,
  "دليل المبيعات الداخلي — سولنت",
  "قواعد تجارية مختصرة لتوحيد عروض الأسعار المقدمة للزبائن",
  "F",
);

function writeRtlKeyValueRows(sheet, startRow, rows, recommendedRow = null) {
  rows.forEach((entry, index) => {
    const row = startRow + index;
    sheet.getRange(`A${row}:E${row}`).merge();
    sheet.getRange(`A${row}`).values = [[entry[1]]];
    sheet.getRange(`F${row}`).values = [[entry[0]]];
    const highlight = row === recommendedRow;
    sheet.getRange(`A${row}:F${row}`).format = {
      fill: highlight ? colors.tealLight : colors.soft,
      font: { name: "Arial", color: highlight ? colors.teal : colors.slate, size: 10, bold: highlight },
      verticalAlignment: "center",
      horizontalAlignment: "right",
      wrapText: true,
      borders: { bottom: { style: "thin", color: colors.line } },
    };
    sheet.getRange(`F${row}`).format.font = { name: "Arial", bold: true, color: highlight ? colors.teal : colors.slate, size: 10 };
  });
}

applySectionHeader(sales, "A4:F4", "مرجع سريع");
writeRtlKeyValueRows(sales, 5, [
  ["الخيار الافتراضي", "الباقة الموصى بها: Plus — شهرياً 70 د.أ / سنوياً 700 د.أ / الرخصة الدائمة 1,500 د.أ"],
  ["الفترة التجريبية", "تجربة Plus لمدة 14 يوماً"],
  ["العرض السنوي", "شهران مجاناً مقارنة بالدفع الشهري"],
  ["أساس التسعير", "لكل مختبر، وليس حسب عدد الموظفين أو المستخدمين"],
  ["الخصومات", "أي خصم إضافي يحتاج موافقة المالك"],
  ["العمل المخصص", "التطوير المخصص، الزيارات الميدانية ونقل البيانات المعقّد تُسعّر منفصلة"],
], 5);
sales.getRange("A5:F10").format.rowHeight = 31;

applySectionHeader(sales, "A12:F12", "شروط الاشتراك");
writeRtlKeyValueRows(sales, 13, [
  ["التحديثات والدعم", "مشمولة طوال مدة الاشتراك الفعّال"],
  ["الاستضافة", "مشمولة عند استخدام سيرفر مُدار من SOLENT"],
  ["الدفع", "دفع شهري أو سنوي"],
  ["قيمة الاشتراك السنوي", "يُحتسب بسعر عشرة أشهر"],
]);
sales.getRange("A13:F16").format.rowHeight = 29;

applySectionHeader(sales, "A18:F18", "شروط الرخصة الدائمة");
writeRtlKeyValueRows(sales, 19, [
  ["نطاق الرخصة", "مختبر واحد ونسخة تشغيل فعلية واحدة"],
  ["المشمول", "التركيب، التدريب، التحديثات والدعم لمدة 12 شهراً"],
  ["بعد 12 شهراً", "تستمر النسخة المثبتة بالعمل بشكل دائم"],
  ["الصيانة", "صيانة سنوية اختيارية حسب سعر الباقة"],
  ["سيرفر الزبون", "تكاليف VPS والاستضافة والنسخ الاحتياطي وإدارة السيرفر غير مشمولة"],
  ["الملكية", "لا تشمل ملكية الكود أو حقوق إعادة البيع أو نقل الملكية الفكرية"],
]);
sales.getRange("A19:F24").format.rowHeight = 31;

applySectionHeader(sales, "A26:F26", "قواعد المبيعات");
const salesRules = [
  ["1", "رشّح Plus ما لم تكن احتياجات الزبون مناسبة بوضوح لباقة أخرى."],
  ["2", "اختر الباقة حسب الخصائص والخدمة المطلوبة، وليس حسب القدرة المالية المتوقعة للزبون."],
  ["3", "لا تعد بخصومات أو خصائص مخصصة أو موعد تسليم دون موافقة المالك."],
  ["4", "وضّح أن الرخصة الدائمة هي حق استخدام وليست ملكية للبرنامج أو للكود."],
];
salesRules.forEach((entry, index) => {
  const row = 27 + index;
  sales.getRange(`A${row}:E${row}`).merge();
  sales.getRange(`A${row}`).values = [[entry[1]]];
  sales.getRange(`F${row}`).values = [[entry[0]]];
  sales.getRange(`A${row}:E${row}`).format = {
    fill: colors.soft,
    font: { name: "Arial", color: colors.slate, size: 10 },
    verticalAlignment: "center",
    horizontalAlignment: "right",
    wrapText: true,
    borders: { bottom: { style: "thin", color: colors.line } },
  };
  sales.getRange(`F${row}`).format = {
    fill: colors.tealLight,
    font: { name: "Arial", bold: true, color: colors.teal, size: 11 },
    verticalAlignment: "center",
    horizontalAlignment: "center",
    borders: { bottom: { style: "thin", color: colors.line } },
  };
});
sales.getRange("A27:F30").format.rowHeight = 36;

applySectionHeader(sales, "A32:F32", "صياغة سريعة للبيع");
sales.getRange("A33:F35").merge();
sales.getRange("A33").values = [[
  "تبدأ باقاتنا من 50 ديناراً شهرياً، وأكثر خيار يتم اختياره هو Plus بسعر 70 ديناراً شهرياً أو 700 دينار سنوياً. كما تتوفر رخص دائمة تبدأ من 1,000 دينار. نرشح الباقة الأنسب حسب احتياجات المختبر ومستوى الدعم المطلوب.",
]];
sales.getRange("A33:F35").format = {
  fill: colors.tealLight,
  font: { name: "Arial", color: colors.slate, size: 11, italic: true },
  verticalAlignment: "center",
  horizontalAlignment: "right",
  wrapText: true,
  borders: { preset: "outside", style: "thin", color: colors.teal },
};
sales.getRange("A33:F35").format.rowHeight = 22;

sales.getRange("A1:E35").format.columnWidth = 18;
sales.getRange("F1:F35").format.columnWidth = 25;
sales.freezePanes.freezeRows(4);

const pricingCheck = await workbook.inspect({
  kind: "table",
  range: "'الأسعار والباقات'!A1:G18",
  include: "values,formulas",
  tableMaxRows: 20,
  tableMaxCols: 8,
});
console.log("PRICING_CHECK");
console.log(pricingCheck.ndjson);

const salesCheck = await workbook.inspect({
  kind: "table",
  range: "'دليل المبيعات'!A1:F35",
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
  ["الأسعار والباقات", "pricing-ar-preview.png", "A1:G18"],
  ["دليل المبيعات", "sales-guide-ar-preview.png", "A1:F35"],
]) {
  const preview = await workbook.render({ sheetName, range, scale: 1.4, format: "png" });
  await fs.writeFile(`${outputDir}/${fileName}`, new Uint8Array(await preview.arrayBuffer()));
}

const output = await SpreadsheetFile.exportXlsx(workbook);
await output.save(`${outputDir}/Solent_Internal_Price_List_AR.xlsx`);
console.log(`OUTPUT=${outputDir}/Solent_Internal_Price_List_AR.xlsx`);
