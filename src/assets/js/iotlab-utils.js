
// Return the firmware types allowed for a given node architecture

export const allowedFirmwares4Archi = function (archi) {
  archi = archi.split(':')[0].toLowerCase()
  switch (archi) {
    case 'a8':
      return []
    case 'wsn430':
      return ['hex', 'ihex']
    default:
      return ['elf']
  }
}

// Extract archi from a string "archi:radio"
export const extractArchi = function (nodeArchi) {
  return nodeArchi.split(':')[0]
}

// List of existing experiment states
export const experimentStates = {
  all: 'Terminated,Stopped,Error,Running,Finishing,Resuming,toError,Waiting,Launching,Hold,toLaunch,toAckReservation,Suspended'.split(','),
  scheduled: 'Running,Finishing,Resuming,toError,Waiting,Launching,Hold,toLaunch,toAckReservation,Suspended'.split(','),
  terminated: 'Terminated,Stopped,Error'.split(','),
  stoppable: 'Running,Waiting'.split(','),
}