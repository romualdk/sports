/* global fetch */

import { RunningDistanceParser } from './js/RunningDistanceParser.js'
import { filterByYear, flatten, chartDataByYearMonthDay, chartHtmlByYearMonthDayHorizontal } from './js/Chart.js'
import { getStates } from './js/Statistics.js'

const filepath = 'data/endomondo-workouts-r.json'

fetch(filepath)
  .then(response => response.json())
  .then(data => parse(data))

function parse (data) {
  let arr = RunningDistanceParser(data)
  let chartData = chartDataByYearMonthDay(arr.values)

  /*
  let year = 2019
  let filtered = filterByYear(chartData, year)
  let flat = flatten(filtered)
  let states = getStates(flat)
  */

  let content = chartHtmlByYearMonthDayHorizontal(chartData)
  document.getElementsByTagName('body')[0].innerHTML += content
}
