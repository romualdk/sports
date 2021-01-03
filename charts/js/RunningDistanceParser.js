const TYPE = 'running'
const METRIC = 'distance'
const UNIT = 'km'

export function RunningDistanceParser (data) {
  let arr = data.filter(function (row) {
    return row.type === TYPE
  })

  return {
    type: TYPE,
    metric: METRIC,
    unit: UNIT,
    values: arr.map(function (row) {
      return {
        date: row.start,
        year: row.year,
        month: row.month,
        day: row.day,
        value: row[METRIC] / 1000
      }
    })
  }
}
