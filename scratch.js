const NepaliDate = require('nepali-date');
const d = new NepaliDate(2083, 1, 1);
const end = new NepaliDate(2083, 2, 0); // 0th day of next month is the last day of this month
console.log(d.format('yyyy-mm-dd'), end.format('yyyy-mm-dd'));
