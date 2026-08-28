import fs from "node:fs/promises";
import path from "node:path";
import { SpreadsheetFile, Workbook } from "@oai/artifact-tool";

const outputDir = "C:/Users/Yazan/Desktop/Projects/Solent/app/outputs/lead-crm-20260819-english";
const previewDir = "C:/Users/Yazan/Desktop/Projects/Solent/app/.codex_tmp/lead-crm-workbook/previews";
const outputPath = path.join(outputDir, "Lead_Follow_Up_Tracker.xlsx");

await fs.mkdir(outputDir, { recursive: true });
await fs.mkdir(previewDir, { recursive: true });

const workbook = Workbook.create();
const dashboard = workbook.worksheets.add("Dashboard");
const leads = workbook.worksheets.add("Leads");
const activity = workbook.worksheets.add("Activity Log");
const lists = workbook.worksheets.add("Lists & Rules");
const guide = workbook.worksheets.add("Quick Start");

const colors = {
  navy: "#0B1F33",
  navy2: "#123452",
  blue: "#2563EB",
  blueLight: "#DBEAFE",
  teal: "#0F766E",
  tealLight: "#CCFBF1",
  green: "#16A34A",
  greenLight: "#DCFCE7",
  amber: "#D97706",
  amberLight: "#FEF3C7",
  red: "#DC2626",
  redLight: "#FEE2E2",
  purple: "#7C3AED",
  purpleLight: "#EDE9FE",
  slate: "#475569",
  slateLight: "#E2E8F0",
  gray: "#64748B",
  border: "#CBD5E1",
  canvas: "#F4F7FB",
  white: "#FFFFFF",
  input: "#FFFBEA",
  formula: "#EFF6FF",
};

const bodyFont = { name: "Aptos", size: 10, color: colors.navy };
const titleFont = { name: "Aptos Display", size: 22, bold: true, color: colors.white };
const sectionHeader = {
  fill: colors.navy,
  font: { name: "Aptos", size: 10, bold: true, color: colors.white },
  verticalAlignment: "center",
  wrapText: true,
};
const thinBottom = {
  bottom: { style: "thin", color: colors.border },
};

function setWidths(sheet, widths, maxRow) {
  for (const [column, width] of Object.entries(widths)) {
    sheet.getRange(`${column}1:${column}${maxRow}`).format.columnWidth = width;
  }
}

function styleTitle(sheet, range, title, subtitleRange, subtitle) {
  sheet.getRange(range).merge();
  sheet.getRange(range.split(":")[0]).values = [[title]];
  sheet.getRange(range).format = {
    fill: colors.navy,
    font: titleFont,
    verticalAlignment: "center",
  };
  sheet.getRange(range).format.rowHeight = 34;
  sheet.getRange(subtitleRange).merge();
  sheet.getRange(subtitleRange.split(":")[0]).values = [[subtitle]];
  sheet.getRange(subtitleRange).format = {
    fill: colors.navy2,
    font: { name: "Aptos", size: 10, color: "#DCE8F5" },
    verticalAlignment: "center",
  };
  sheet.getRange(subtitleRange).format.rowHeight = 24;
}

function addKpiCard(sheet, labelRange, valueRange, label, formula, accent) {
  sheet.getRange(labelRange).merge();
  sheet.getRange(labelRange.split(":")[0]).values = [[label]];
  sheet.getRange(labelRange).format = {
    fill: accent,
    font: { name: "Aptos", size: 9, bold: true, color: colors.white },
    horizontalAlignment: "center",
    verticalAlignment: "center",
    borders: { preset: "outside", style: "thin", color: accent },
  };
  sheet.getRange(valueRange).merge();
  sheet.getRange(valueRange.split(":")[0]).formulas = [[formula]];
  sheet.getRange(valueRange).format = {
    fill: colors.white,
    font: { name: "Aptos Display", size: 20, bold: true, color: colors.navy },
    horizontalAlignment: "center",
    verticalAlignment: "center",
    borders: { preset: "outside", style: "thin", color: accent },
  };
}

// Lists & Rules: visible and editable source of truth for every dropdown and score.
lists.showGridLines = false;
styleTitle(
  lists,
  "A1:T1",
  "Lists & Rules",
  "A2:T2",
  "Edit these lists to customize dropdowns, owners, scoring, and stage probabilities."
);

const listColumns = [
  ["Statuses", ["Active", "Nurturing", "On Hold", "Won", "Lost", "Disqualified"]],
  ["Interest", ["Yes", "Maybe", "No", "Unknown"]],
  ["Stages", ["New", "Attempting Contact", "Contacted", "Qualified", "Demo Scheduled", "Proposal Sent", "Negotiation", "Won", "Lost"]],
  ["Priorities", ["High", "Medium", "Low"]],
  ["Lead Sources", ["Referral", "WhatsApp", "Phone", "Website", "Instagram", "Facebook", "LinkedIn", "Event", "Walk-in", "Other"]],
  ["Preferred Contact", ["WhatsApp", "Phone", "Email", "In-person"]],
  ["Activity Channels", ["WhatsApp", "Phone Call", "Email", "Meeting", "Instagram", "Other"]],
  ["Direction", ["Outbound", "Inbound"]],
  ["Activity Outcomes", ["No reply", "Replied", "Interested", "Follow-up needed", "Demo booked", "Proposal sent", "Not interested", "Won", "Lost"]],
  ["Owners", ["Yazan", "Sales"]],
  ["Yes / No", ["No", "Yes"]],
];

listColumns.forEach(([header, values], index) => {
  const column = String.fromCharCode(65 + index);
  lists.getRange(`${column}4`).values = [[header]];
  lists.getRange(`${column}4`).format = sectionHeader;
  lists.getRange(`${column}5:${column}${4 + values.length}`).values = values.map((value) => [value]);
  lists.getRange(`${column}5:${column}${4 + values.length}`).format = {
    fill: colors.input,
    font: bodyFont,
    borders: thinBottom,
  };
});

lists.getRange("L4:N4").values = [["Stage", "Win Probability", "Stage Score"]];
lists.getRange("L4:N4").format = sectionHeader;
lists.getRange("L5:N13").values = [
  ["New", 0.05, 5],
  ["Attempting Contact", 0.1, 7],
  ["Contacted", 0.15, 10],
  ["Qualified", 0.3, 15],
  ["Demo Scheduled", 0.45, 20],
  ["Proposal Sent", 0.6, 25],
  ["Negotiation", 0.8, 30],
  ["Won", 1, 35],
  ["Lost", 0, 0],
];
lists.getRange("L5:N13").format = { fill: colors.input, font: bodyFont, borders: thinBottom };
lists.getRange("M5:M13").format.numberFormat = "0%";

lists.getRange("P4:Q4").values = [["Interest", "Interest Score"]];
lists.getRange("P4:Q4").format = sectionHeader;
lists.getRange("P5:Q8").values = [["Yes", 30], ["Maybe", 15], ["No", 0], ["Unknown", 0]];
lists.getRange("P5:Q8").format = { fill: colors.input, font: bodyFont, borders: thinBottom };

lists.getRange("S4:T4").values = [["Priority", "Priority Score"]];
lists.getRange("S4:T4").format = sectionHeader;
lists.getRange("S5:T7").values = [["High", 20], ["Medium", 10], ["Low", 5]];
lists.getRange("S5:T7").format = { fill: colors.input, font: bodyFont, borders: thinBottom };

lists.getRange("L16:T19").merge();
lists.getRange("L16").values = [[
  "Lead Score = Interest Score + Priority Score + Stage Score + follow-up adjustment + activity points. " +
  "Scores are capped between 0 and 100. Edit the yellow mapping cells above to change the model."
]];
lists.getRange("L16:T19").format = {
  fill: colors.blueLight,
  font: { name: "Aptos", size: 10, color: colors.navy },
  wrapText: true,
  verticalAlignment: "center",
  borders: { preset: "outside", style: "thin", color: colors.blue },
};
setWidths(lists, {
  A: 16, B: 13, C: 20, D: 13, E: 16, F: 19, G: 19, H: 13, I: 22, J: 15, K: 12,
  L: 20, M: 17, N: 14, O: 3, P: 15, Q: 16, R: 3, S: 15, T: 16,
}, 25);
lists.freezePanes.freezeRows(4);

// Activity Log: every message, call, meeting, or follow-up belongs here.
activity.showGridLines = false;
styleTitle(
  activity,
  "A1:M1",
  "Activity Log",
  "A2:M2",
  "Log every message, call, meeting, and reply. Pending follow-ups automatically flow into the Leads sheet."
);
activity.getRange("A3:M3").merge();
activity.getRange("A3").values = [["Yellow cells are inputs. Blue cells are formulas. Mark a follow-up Yes only after it is completed, then log the new interaction."]];
activity.getRange("A3:M3").format = {
  fill: colors.amberLight,
  font: { name: "Aptos", size: 9, color: "#7C2D12" },
  verticalAlignment: "center",
};

const activityHeaders = [
  "Activity ID", "Date & Time", "Lead ID", "Contact Name", "Channel", "Direction",
  "Summary / Message", "Outcome", "Next Follow-Up", "Follow-Up Completed?", "Owner",
  "Link / Attachment", "Notes",
];
activity.getRange("A5:M5").values = [activityHeaders];
activity.getRange("A5:M5").format = sectionHeader;
activity.getRange("A5:M5").format.rowHeight = 34;

activity.getRange("D6").formulas = [["=IF($C6=\"\",\"\",IFERROR(VLOOKUP($C6,'Leads'!$A$6:$C$505,3,FALSE),\"Lead ID not found\"))"]];
activity.getRange("D6:D1005").fillDown();
activity.getRange("A6:M1005").format = {
  font: bodyFont,
  verticalAlignment: "center",
  borders: { insideHorizontal: { style: "thin", color: "#E8EEF5" } },
};
activity.getRange("A6:C1005").format.fill = colors.input;
activity.getRange("D6:D1005").format.fill = colors.formula;
activity.getRange("E6:M1005").format.fill = colors.input;
activity.getRange("B6:B1005").format.numberFormat = "yyyy-mm-dd hh:mm";
activity.getRange("I6:I1005").format.numberFormat = "yyyy-mm-dd hh:mm";
activity.getRange("G6:G1005").format.wrapText = true;
activity.getRange("M6:M1005").format.wrapText = true;

activity.getRange("C6:C1005").dataValidation = { rule: { type: "list", formula1: "'Leads'!$A$6:$A$505" } };
activity.getRange("E6:E1005").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$G$5:$G$10" } };
activity.getRange("F6:F1005").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$H$5:$H$6" } };
activity.getRange("H6:H1005").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$I$5:$I$13" } };
activity.getRange("J6:J1005").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$K$5:$K$6" } };
activity.getRange("K6:K1005").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$J$5:$J$14" } };
activity.getRange("J6:J1005").conditionalFormats.add("containsText", {
  text: "No",
  format: { fill: colors.amberLight, font: { bold: true, color: "#92400E" } },
});
activity.getRange("J6:J1005").conditionalFormats.add("containsText", {
  text: "Yes",
  format: { fill: colors.greenLight, font: { bold: true, color: "#166534" } },
});
activity.getRange("A6:A1005").conditionalFormats.addCustom(
  "=AND($A6<>\"\",COUNTIF($A$6:$A$1005,$A6)>1)",
  { fill: colors.redLight, font: { bold: true, color: "#991B1B" } }
);
const activityTable = activity.tables.add("A5:M1005", true, "LeadActivityLog");
activityTable.style = "TableStyleMedium2";
activityTable.showFilterButton = true;
setWidths(activity, {
  A: 15, B: 19, C: 14, D: 22, E: 17, F: 13, G: 38, H: 20, I: 19, J: 21, K: 15, L: 26, M: 32,
}, 1005);
activity.freezePanes.freezeRows(5);
activity.freezePanes.freezeColumns(3);

// Leads: master CRM tracker with formula-driven follow-up and scoring fields.
leads.showGridLines = false;
styleTitle(
  leads,
  "A1:AB1",
  "Lead Tracker",
  "A2:AB2",
  "Add each lead once, then use Activity Log for every message. Filters and formulas keep the next action visible."
);
leads.getRange("A3:AB3").merge();
leads.getRange("A3").values = [["Yellow = enter or choose data  |  Blue = calculated automatically  |  Start each day by filtering Follow-Up Status for Overdue and Today."]];
leads.getRange("A3:AB3").format = {
  fill: colors.amberLight,
  font: { name: "Aptos", size: 9, bold: true, color: "#7C2D12" },
  verticalAlignment: "center",
};

const leadHeaders = [
  "Lead ID", "Date Added", "Contact Name", "Organization", "Phone", "WhatsApp", "Email", "City",
  "Lead Source", "Owner", "Status", "Interested?", "Stage", "Priority", "Lead Score", "Last Contact",
  "Next Follow-Up", "Follow-Up Status", "Days Since Contact", "Preferred Contact", "Next Action",
  "Last Message / Summary", "Potential Value (JOD)", "Win Probability", "Weighted Value (JOD)",
  "Activity Count", "Closed Date", "Notes",
];
leads.getRange("A5:AB5").values = [leadHeaders];
leads.getRange("A5:AB5").format = sectionHeader;
leads.getRange("A5:AB5").format.rowHeight = 38;

const leadFormulas = {
  O: "=IF($C6=\"\",\"\",MAX(0,MIN(100,IFERROR(VLOOKUP($L6,'Lists & Rules'!$P$5:$Q$8,2,FALSE),0)+IFERROR(VLOOKUP($N6,'Lists & Rules'!$S$5:$T$7,2,FALSE),0)+IFERROR(VLOOKUP($M6,'Lists & Rules'!$L$5:$N$13,3,FALSE),0)+IF($R6=\"Overdue\",-10,IF($R6=\"Today\",5,0))+MIN(15,$Z6*3))))",
  P: "=IF($A6=\"\",\"\",IF(COUNTIF('Activity Log'!$C$6:$C$1005,$A6)=0,\"\",MAXIFS('Activity Log'!$B$6:$B$1005,'Activity Log'!$C$6:$C$1005,$A6)))",
  Q: "=IF($A6=\"\",\"\",IF(COUNTIFS('Activity Log'!$C$6:$C$1005,$A6,'Activity Log'!$J$6:$J$1005,\"No\",'Activity Log'!$I$6:$I$1005,\">0\")=0,\"\",MINIFS('Activity Log'!$I$6:$I$1005,'Activity Log'!$C$6:$C$1005,$A6,'Activity Log'!$J$6:$J$1005,\"No\",'Activity Log'!$I$6:$I$1005,\">0\")))",
  R: "=IF($C6=\"\",\"\",IF(OR($K6=\"Won\",$K6=\"Lost\",$K6=\"Disqualified\"),\"Closed\",IF($Q6=\"\",\"No date\",IF(INT($Q6)<TODAY(),\"Overdue\",IF(INT($Q6)=TODAY(),\"Today\",IF(INT($Q6)<=TODAY()+7,\"Next 7 days\",\"Later\"))))))",
  S: "=IF(OR($C6=\"\",$P6=\"\"),\"\",TODAY()-INT($P6))",
  X: "=IF($C6=\"\",\"\",IFERROR(VLOOKUP($M6,'Lists & Rules'!$L$5:$N$13,2,FALSE),0))",
  Y: "=IF(OR($C6=\"\",$W6=\"\"),\"\",ROUND($W6*$X6,0))",
  Z: "=IF($A6=\"\",\"\",COUNTIF('Activity Log'!$C$6:$C$1005,$A6))",
};

for (const [column, formula] of Object.entries(leadFormulas)) {
  leads.getRange(`${column}6`).formulas = [[formula]];
  leads.getRange(`${column}6:${column}505`).fillDown();
}

leads.getRange("A6:AB505").format = {
  font: bodyFont,
  verticalAlignment: "center",
  borders: { insideHorizontal: { style: "thin", color: "#E8EEF5" } },
};
leads.getRange("A6:N505").format.fill = colors.input;
leads.getRange("O6:S505").format.fill = colors.formula;
leads.getRange("T6:W505").format.fill = colors.input;
leads.getRange("X6:Z505").format.fill = colors.formula;
leads.getRange("AA6:AB505").format.fill = colors.input;
leads.getRange("B6:B505").format.numberFormat = "yyyy-mm-dd";
leads.getRange("P6:Q505").format.numberFormat = "yyyy-mm-dd hh:mm";
leads.getRange("W6:W505").format.numberFormat = "#,##0";
leads.getRange("X6:X505").format.numberFormat = "0%";
leads.getRange("Y6:Y505").format.numberFormat = "#,##0";
leads.getRange("AA6:AA505").format.numberFormat = "yyyy-mm-dd";
leads.getRange("V6:V505").format.wrapText = true;
leads.getRange("AB6:AB505").format.wrapText = true;

leads.getRange("I6:I505").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$E$5:$E$14" } };
leads.getRange("J6:J505").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$J$5:$J$14" } };
leads.getRange("K6:K505").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$A$5:$A$10" } };
leads.getRange("L6:L505").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$B$5:$B$8" } };
leads.getRange("M6:M505").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$C$5:$C$13" } };
leads.getRange("N6:N505").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$D$5:$D$7" } };
leads.getRange("T6:T505").dataValidation = { rule: { type: "list", formula1: "'Lists & Rules'!$F$5:$F$8" } };

leads.getRange("A6:A505").conditionalFormats.addCustom(
  "=AND($A6<>\"\",COUNTIF($A$6:$A$505,$A6)>1)",
  { fill: colors.redLight, font: { bold: true, color: "#991B1B" } }
);
leads.getRange("O6:O505").conditionalFormats.addCustom(
  "=AND($O6<>\"\",$O6<40)",
  { fill: colors.redLight, font: { bold: true, color: "#991B1B" } }
);
leads.getRange("O6:O505").conditionalFormats.addCustom(
  "=AND($O6<>\"\",$O6>=40,$O6<70)",
  { fill: colors.amberLight, font: { bold: true, color: "#92400E" } }
);
leads.getRange("O6:O505").conditionalFormats.addCustom(
  "=AND($O6<>\"\",$O6>=70)",
  { fill: colors.greenLight, font: { bold: true, color: "#166534" } }
);
leads.getRange("R6:R505").conditionalFormats.add("containsText", {
  text: "Overdue",
  format: { fill: colors.redLight, font: { bold: true, color: "#991B1B" } },
});
leads.getRange("R6:R505").conditionalFormats.add("containsText", {
  text: "Today",
  format: { fill: colors.amberLight, font: { bold: true, color: "#92400E" } },
});
leads.getRange("R6:R505").conditionalFormats.add("containsText", {
  text: "Next 7 days",
  format: { fill: colors.blueLight, font: { bold: true, color: "#1E40AF" } },
});
leads.getRange("R6:R505").conditionalFormats.add("containsText", {
  text: "Closed",
  format: { fill: colors.slateLight, font: { color: colors.slate } },
});
leads.getRange("L6:L505").conditionalFormats.add("containsText", {
  text: "Yes",
  format: { fill: colors.greenLight, font: { bold: true, color: "#166534" } },
});
leads.getRange("N6:N505").conditionalFormats.add("containsText", {
  text: "High",
  format: { fill: colors.redLight, font: { bold: true, color: "#991B1B" } },
});
const leadsTable = leads.tables.add("A5:AB505", true, "LeadsTracker");
leadsTable.style = "TableStyleMedium2";
leadsTable.showFilterButton = true;
setWidths(leads, {
  A: 14, B: 13, C: 22, D: 24, E: 16, F: 16, G: 27, H: 14, I: 16, J: 15, K: 15, L: 13,
  M: 20, N: 12, O: 12, P: 18, Q: 18, R: 17, S: 16, T: 17, U: 24, V: 38, W: 20, X: 16,
  Y: 20, Z: 15, AA: 14, AB: 34,
}, 505);
leads.freezePanes.freezeRows(5);
leads.freezePanes.freezeColumns(2);

// Dashboard: formula-driven overview of the live Leads table.
dashboard.showGridLines = false;
dashboard.getRange("A1:M1").merge();
dashboard.getRange("A1").values = [["Lead & Follow-Up Dashboard"]];
dashboard.getRange("A1:M1").format = {
  fill: colors.navy,
  font: titleFont,
  verticalAlignment: "center",
};
dashboard.getRange("A1:M1").format.rowHeight = 38;
dashboard.getRange("A2:M2").merge();
dashboard.getRange("A2").formulas = [["=\"Live summary as of \"&TEXT(TODAY(),\"dd mmm yyyy\")&\" — update the Leads and Activity Log sheets; this page updates automatically.\""]];
dashboard.getRange("A2:M2").format = {
  fill: colors.navy2,
  font: { name: "Aptos", size: 10, color: "#DCE8F5" },
  verticalAlignment: "center",
};

addKpiCard(dashboard, "A4:B4", "A5:B6", "TOTAL LEADS", "=COUNTIF('Leads'!$C$6:$C$505,\"<>\")", colors.blue);
addKpiCard(dashboard, "C4:D4", "C5:D6", "ACTIVE LEADS", "=COUNTIFS('Leads'!$C$6:$C$505,\"<>\",'Leads'!$K$6:$K$505,\"<>Won\",'Leads'!$K$6:$K$505,\"<>Lost\",'Leads'!$K$6:$K$505,\"<>Disqualified\")", colors.teal);
addKpiCard(dashboard, "E4:F4", "E5:F6", "INTERESTED", "=COUNTIFS('Leads'!$C$6:$C$505,\"<>\",'Leads'!$L$6:$L$505,\"Yes\")", colors.green);
addKpiCard(dashboard, "G4:H4", "G5:H6", "OVERDUE", "=COUNTIF('Leads'!$R$6:$R$505,\"Overdue\")", colors.red);
addKpiCard(dashboard, "I4:J4", "I5:J6", "DUE TODAY", "=COUNTIF('Leads'!$R$6:$R$505,\"Today\")", colors.amber);
addKpiCard(dashboard, "K4:L4", "K5:L6", "WON", "=COUNTIF('Leads'!$K$6:$K$505,\"Won\")", colors.purple);

dashboard.getRange("A8:D8").merge();
dashboard.getRange("A8").values = [["Pipeline Overview"]];
dashboard.getRange("A8:D8").format = sectionHeader;
dashboard.getRange("A9:D9").values = [["Stage", "Leads", "Potential JOD", "Weighted JOD"]];
dashboard.getRange("A9:D9").format = {
  fill: colors.slateLight,
  font: { name: "Aptos", size: 9, bold: true, color: colors.navy },
  borders: { preset: "all", style: "thin", color: colors.border },
};
const stages = ["New", "Attempting Contact", "Contacted", "Qualified", "Demo Scheduled", "Proposal Sent", "Negotiation", "Won", "Lost"];
dashboard.getRange("A10:A18").values = stages.map((stage) => [stage]);
for (let row = 10; row <= 18; row += 1) {
  dashboard.getRange(`B${row}`).formulas = [[`=COUNTIF('Leads'!$M$6:$M$505,$A${row})`]];
  dashboard.getRange(`C${row}`).formulas = [[`=SUMIF('Leads'!$M$6:$M$505,$A${row},'Leads'!$W$6:$W$505)`]];
  dashboard.getRange(`D${row}`).formulas = [[`=SUMIF('Leads'!$M$6:$M$505,$A${row},'Leads'!$Y$6:$Y$505)`]];
}
dashboard.getRange("A10:D18").format = { fill: colors.white, font: bodyFont, borders: thinBottom };
dashboard.getRange("B10:B18").format.numberFormat = "#,##0";
dashboard.getRange("C10:D18").format.numberFormat = "#,##0";

const pipelineChart = dashboard.charts.add("bar", dashboard.getRange("A9:B18"));
pipelineChart.title = "Leads by Pipeline Stage";
pipelineChart.hasLegend = false;
pipelineChart.xAxis = { axisType: "textAxis", textStyle: { fontSize: 9 } };
pipelineChart.yAxis = { numberFormatCode: "0" };
pipelineChart.setPosition("F8", "M21");

dashboard.getRange("A21:B21").merge();
dashboard.getRange("A21").values = [["Follow-Up Workload"]];
dashboard.getRange("A21:B21").format = sectionHeader;
dashboard.getRange("A22:B22").values = [["Follow-Up Status", "Leads"]];
dashboard.getRange("A22:B22").format = {
  fill: colors.slateLight,
  font: { name: "Aptos", size: 9, bold: true, color: colors.navy },
  borders: { preset: "all", style: "thin", color: colors.border },
};
const followUps = ["Overdue", "Today", "Next 7 days", "Later", "No date", "Closed"];
dashboard.getRange("A23:A28").values = followUps.map((value) => [value]);
for (let row = 23; row <= 28; row += 1) {
  dashboard.getRange(`B${row}`).formulas = [[`=COUNTIF('Leads'!$R$6:$R$505,$A${row})`]];
}
dashboard.getRange("A23:B28").format = { fill: colors.white, font: bodyFont, borders: thinBottom };
dashboard.getRange("B23:B28").format.numberFormat = "#,##0";
dashboard.getRange("A23:B23").conditionalFormats.add("cellIs", {
  operator: "greaterThan",
  formula: 0,
  format: { fill: colors.redLight, font: { bold: true, color: "#991B1B" } },
});

dashboard.getRange("D21:E21").merge();
dashboard.getRange("D21").values = [["Interest"]];
dashboard.getRange("D21:E21").format = sectionHeader;
dashboard.getRange("D22:E22").values = [["Response", "Leads"]];
dashboard.getRange("D22:E22").format = {
  fill: colors.slateLight,
  font: { name: "Aptos", size: 9, bold: true, color: colors.navy },
  borders: { preset: "all", style: "thin", color: colors.border },
};
const interestLevels = ["Yes", "Maybe", "No", "Unknown"];
dashboard.getRange("D23:D26").values = interestLevels.map((value) => [value]);
for (let row = 23; row <= 26; row += 1) {
  dashboard.getRange(`E${row}`).formulas = [[`=COUNTIF('Leads'!$L$6:$L$505,$D${row})`]];
}
dashboard.getRange("D23:E26").format = { fill: colors.white, font: bodyFont, borders: thinBottom };

dashboard.getRange("A31:B31").merge();
dashboard.getRange("A31").values = [["Lead Sources"]];
dashboard.getRange("A31:B31").format = sectionHeader;
dashboard.getRange("A32:B32").values = [["Source", "Leads"]];
dashboard.getRange("A32:B32").format = {
  fill: colors.slateLight,
  font: { name: "Aptos", size: 9, bold: true, color: colors.navy },
  borders: { preset: "all", style: "thin", color: colors.border },
};
const sources = ["Referral", "WhatsApp", "Phone", "Website", "Instagram", "Facebook", "LinkedIn", "Event", "Walk-in", "Other"];
dashboard.getRange("A33:A42").values = sources.map((source) => [source]);
for (let row = 33; row <= 42; row += 1) {
  dashboard.getRange(`B${row}`).formulas = [[`=COUNTIF('Leads'!$I$6:$I$505,$A${row})`]];
}
dashboard.getRange("A33:B42").format = { fill: colors.white, font: bodyFont, borders: thinBottom };

dashboard.getRange("D31:M31").merge();
dashboard.getRange("D31").values = [["Daily Routine"]];
dashboard.getRange("D31:M31").format = sectionHeader;
dashboard.getRange("D32:M38").merge();
dashboard.getRange("D32").values = [[
  "1. Open Dashboard and check Overdue + Due Today.\n" +
  "2. Filter Leads by Follow-Up Status and contact those people first.\n" +
  "3. Add every message or call to Activity Log.\n" +
  "4. Enter the next follow-up date before leaving the activity row.\n" +
  "5. Mark completed follow-ups Yes and add the new interaction."
]];
dashboard.getRange("D32:M38").format = {
  fill: colors.blueLight,
  font: { name: "Aptos", size: 11, color: colors.navy },
  wrapText: true,
  verticalAlignment: "center",
  borders: { preset: "outside", style: "thin", color: colors.blue },
};

dashboard.getRange("D40:M42").merge();
dashboard.getRange("D40").formulas = [["=IF($G$5>0,\"ACTION REQUIRED: \"&$G$5&\" overdue follow-up(s). Filter the Leads sheet by Follow-Up Status = Overdue.\",IF($I$5>0,\"TODAY: \"&$I$5&\" follow-up(s) are due.\",\"You are caught up — no overdue or due-today follow-ups.\"))"]];
dashboard.getRange("D40:M42").format = {
  fill: colors.greenLight,
  font: { name: "Aptos", size: 12, bold: true, color: "#166534" },
  wrapText: true,
  verticalAlignment: "center",
  horizontalAlignment: "center",
  borders: { preset: "outside", style: "medium", color: colors.green },
};
dashboard.getRange("D40:M42").conditionalFormats.addCustom("=$G$5>0", {
  fill: colors.redLight,
  font: { bold: true, color: "#991B1B" },
});
dashboard.getRange("D40:M42").conditionalFormats.addCustom("=AND($G$5=0,$I$5>0)", {
  fill: colors.amberLight,
  font: { bold: true, color: "#92400E" },
});

setWidths(dashboard, { A: 18, B: 10, C: 14, D: 14, E: 14, F: 14, G: 14, H: 14, I: 14, J: 14, K: 14, L: 14, M: 14 }, 48);
dashboard.freezePanes.freezeRows(2);

// Quick Start guide.
guide.showGridLines = false;
styleTitle(
  guide,
  "A1:H1",
  "Quick Start — Never Miss a Follow-Up",
  "A2:H2",
  "A simple operating system for leads, messages, calls, demos, proposals, and next actions."
);
guide.getRange("A4:H4").merge();
guide.getRange("A4").values = [["Set up once"]];
guide.getRange("A4:H4").format = sectionHeader;
guide.getRange("A5:B8").values = [
  ["1", "Open Lists & Rules and replace or add owners."],
  ["2", "Adjust stages, sources, scoring, and probabilities only if needed."],
  ["3", "Keep the dropdown ranges intact; add new options inside the prepared list areas."],
  ["4", "Delete any unwanted blank columns only after checking formulas and validations."],
];
guide.getRange("A5:A8").format = {
  fill: colors.blue,
  font: { name: "Aptos", size: 11, bold: true, color: colors.white },
  horizontalAlignment: "center",
  verticalAlignment: "center",
};
guide.getRange("B5:H8").merge(true);
guide.getRange("B5:H8").format = {
  fill: colors.white,
  font: { name: "Aptos", size: 10, color: colors.navy },
  wrapText: true,
  verticalAlignment: "center",
  borders: thinBottom,
};

guide.getRange("A10:H10").merge();
guide.getRange("A10").values = [["For every new lead"]];
guide.getRange("A10:H10").format = sectionHeader;
guide.getRange("A11:B15").values = [
  ["1", "Add one row on Leads and give it a unique Lead ID such as L-0001."],
  ["2", "Set Status, Interested?, Stage, Priority, Owner, and the Next Action."],
  ["3", "Log the first conversation in Activity Log using the exact same Lead ID."],
  ["4", "If another message is needed, enter Next Follow-Up and leave Completed? as No."],
  ["5", "The Lead Score, Last Contact, Follow-Up Status, probability, value, and Activity Count update automatically."],
];
guide.getRange("A11:A15").format = {
  fill: colors.teal,
  font: { name: "Aptos", size: 11, bold: true, color: colors.white },
  horizontalAlignment: "center",
  verticalAlignment: "center",
};
guide.getRange("B11:H15").merge(true);
guide.getRange("B11:H15").format = {
  fill: colors.white,
  font: { name: "Aptos", size: 10, color: colors.navy },
  wrapText: true,
  verticalAlignment: "center",
  borders: thinBottom,
};

guide.getRange("A17:H17").merge();
guide.getRange("A17").values = [["Daily workflow"]];
guide.getRange("A17:H17").format = sectionHeader;
guide.getRange("A18:B22").values = [
  ["1", "Open Dashboard. Overdue follow-ups are your first priority."],
  ["2", "Go to Leads and filter Follow-Up Status by Overdue, then Today."],
  ["3", "After each contact, add a new Activity Log row with a short summary."],
  ["4", "Mark the old follow-up Completed? = Yes and set a new follow-up if needed."],
  ["5", "Move Stage and Status forward when the lead progresses, wins, or closes."],
];
guide.getRange("A18:A22").format = {
  fill: colors.purple,
  font: { name: "Aptos", size: 11, bold: true, color: colors.white },
  horizontalAlignment: "center",
  verticalAlignment: "center",
};
guide.getRange("B18:H22").merge(true);
guide.getRange("B18:H22").format = {
  fill: colors.white,
  font: { name: "Aptos", size: 10, color: colors.navy },
  wrapText: true,
  verticalAlignment: "center",
  borders: thinBottom,
};

guide.getRange("A24:H24").merge();
guide.getRange("A24").values = [["Color key"]];
guide.getRange("A24:H24").format = sectionHeader;
guide.getRange("A25:B25").merge();
guide.getRange("A25").values = [["INPUT CELLS"]];
guide.getRange("A25:B25").format = { fill: colors.input, font: { bold: true, color: colors.navy }, horizontalAlignment: "center" };
guide.getRange("C25:D25").merge();
guide.getRange("C25").values = [["FORMULA CELLS"]];
guide.getRange("C25:D25").format = { fill: colors.formula, font: { bold: true, color: colors.navy }, horizontalAlignment: "center" };
guide.getRange("E25:F25").merge();
guide.getRange("E25").values = [["OVERDUE"]];
guide.getRange("E25:F25").format = { fill: colors.redLight, font: { bold: true, color: "#991B1B" }, horizontalAlignment: "center" };
guide.getRange("G25:H25").merge();
guide.getRange("G25").values = [["DUE TODAY"]];
guide.getRange("G25:H25").format = { fill: colors.amberLight, font: { bold: true, color: "#92400E" }, horizontalAlignment: "center" };

guide.getRange("A28:H32").merge();
guide.getRange("A28").values = [[
  "Important: do not rely on memory or WhatsApp search. The Activity Log is the record of what happened; " +
  "Next Follow-Up is the commitment for what happens next. If either is missing, the lead can disappear from view."
]];
guide.getRange("A28:H32").format = {
  fill: colors.amberLight,
  font: { name: "Aptos", size: 12, bold: true, color: "#7C2D12" },
  wrapText: true,
  verticalAlignment: "center",
  horizontalAlignment: "center",
  borders: { preset: "outside", style: "medium", color: colors.amber },
};
setWidths(guide, { A: 7, B: 18, C: 18, D: 18, E: 18, F: 18, G: 18, H: 18 }, 35);
for (const row of [5, 6, 7, 8, 11, 12, 13, 14, 15, 18, 19, 20, 21, 22]) {
  guide.getRange(`A${row}:H${row}`).format.rowHeight = 34;
}

// Compact verification before export.
const dashboardCheck = await workbook.inspect({
  kind: "table",
  range: "Dashboard!A1:M42",
  include: "values,formulas",
  tableMaxRows: 42,
  tableMaxCols: 13,
  maxChars: 9000,
});
console.log("DASHBOARD_CHECK");
console.log(dashboardCheck.ndjson);

const leadsFormulaCheck = await workbook.inspect({
  kind: "table",
  range: "Leads!A5:AB8",
  include: "values,formulas",
  tableMaxRows: 8,
  tableMaxCols: 28,
  maxChars: 6000,
});
console.log("LEADS_FORMULA_CHECK");
console.log(leadsFormulaCheck.ndjson);

const formulaErrors = await workbook.inspect({
  kind: "match",
  searchTerm: "#REF!|#DIV/0!|#VALUE!|#NAME\\?|#N/A",
  options: { useRegex: true, maxResults: 300 },
  summary: "final formula error scan",
});
console.log("FORMULA_ERRORS");
console.log(formulaErrors.ndjson);

const previews = [
  ["Dashboard", "A1:M42", "dashboard.png", 1.2],
  ["Leads", "A1:AB18", "leads.png", 0.8],
  ["Activity Log", "A1:M20", "activity-log.png", 1.0],
  ["Lists & Rules", "A1:T20", "lists-rules.png", 1.0],
  ["Quick Start", "A1:H32", "quick-start.png", 1.2],
];

for (const [sheetName, range, fileName, scale] of previews) {
  const preview = await workbook.render({ sheetName, range, scale, format: "png" });
  await fs.writeFile(path.join(previewDir, fileName), new Uint8Array(await preview.arrayBuffer()));
}

const output = await SpreadsheetFile.exportXlsx(workbook);
await output.save(outputPath);
console.log(`OUTPUT_PATH=${outputPath}`);
