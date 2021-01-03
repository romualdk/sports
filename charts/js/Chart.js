import { getStates, stateName } from './Statistics.js'

export function chartDataByYearMonthDay (values) {
  let arr = []

  for (let i in values) {
    const { year, month, day, value } = values[i]

    if (!arr[year]) {
      arr[year] = []
    }

    if (!arr[year][month]) {
      arr[year][month] = []
    }

    if (!arr[year][month][day]) {
      arr[year][month][day] = 0
    }

    arr[year][month][day] += value
  }

  return arr
}

export function flatten (chartData) {
  return chartData.map((v) => { return v.flat() }).flat()
}

export function filterByYear (chartData, year) {
  return chartData.filter((v, i) => {
    return i == year
  })
}

const MONTHS = ['STY', 'LUT', 'MAR', 'KWI', 'MAJ', 'CZE', 'LIP', 'SIE', 'WRZ', 'PAŹ', 'LIS', 'GRU']

function daysInMonth (year, month) {
  return new Date(year, month, 0).getDate();
}

export function chartHtmlByYearMonthDayHorizontal (chartData) {
  let html = ``

  for (let year in chartData) {
    html += `<h1>${year}</h1>`
    html += `<table>`

    html += `<thead><tr><th></th>`

    for (let day = 1; day <= 31; day++) {
      html += `<th scope="col">${day}</th>`
    }

    html += `<th scope="col">Total</th>`
    html += `</tr></thead>`

    let states = getStates(flatten(filterByYear(chartData, year)))
    
    for (let month = 1; month <= 12; month++) {
      const days = daysInMonth(year, month)

      html += `<tr>`

      html += `<th class="month" scope="row">${MONTHS[month - 1]}</th>`

      for (let day = 1; day <= days; day++) {
        let value = chartData[year]
          ? chartData[year][month]
            ? chartData[year][month][day] ? chartData[year][month][day] : 0
            : 0
          : 0

        let state = stateName(value, states)
        value = Math.round(value);

        html += `<td class="${state.toLowerCase()}"><div class="cell">${value}</div></td>`
      }

      html += `</tr>`
    }
  }

  html += `</table>`

  return html
}
