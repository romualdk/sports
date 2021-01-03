const RANGES = 5
const NAMES = ['min', 'below', 'normal', 'above', 'max']

export function getStates (nums) {
  let max = Math.max.apply(Math, nums)
  let step = max / RANGES

  return NAMES.map(function (name, i) {
    return [i * step, (i + 1) * step, name]
  })
}

export function stateName (number, states, func = Math.round) {
  if (number === null) {
    return 'empty'
  }

  for (var i in states) {
    let n = func(number)
    let min = func(states[i][0])
    let max = func(states[i][1])

    if (n >= min && n <= max) {
      return states[i][2]
    }
  }

  return 'unknown'
}
