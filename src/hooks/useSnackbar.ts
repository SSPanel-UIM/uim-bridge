import { useState } from 'react'

export const useSnackbar = () => {
  const [open, setOpen] = useState(false)

  const show = () => {
    setOpen(true)
  }

  const hide = () => {
    setOpen(false)
  }

  return {
    show,
    hide,
    open
  }
}
