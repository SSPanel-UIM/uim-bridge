import { useState, FormEvent } from 'react'

export const useInput = <T extends string>(defaultValue: T) => {
  const [value, setValue] = useState(defaultValue)

  const onInput = (e: FormEvent<HTMLDivElement>) => {
    setValue((val) => {
      return (e.target as HTMLInputElement).value as T
    })
  }

  return {
    value,
    onInput
  }
}
