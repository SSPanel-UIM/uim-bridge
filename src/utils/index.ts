import { initAxios } from './axios'

export function classNames(...classes: unknown[]): string {
  return classes.filter(Boolean).join(' ')
}

export const to = (href: string) => {
  window.location.href = href
}

export const init = () => {
  initAxios()
}
