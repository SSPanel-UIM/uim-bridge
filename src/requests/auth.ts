import useSWR from 'swr'
import { apiBaseUrl, login } from '~/requests/routes'
import axios from 'axios'

export interface LoginParams {
  username: () => string
  password: () => string
}

export const useLogin = (params: LoginParams) => {
  const {
    data: loginRes,
    error: loginError,
    mutate: mutateLogin
  } = useSWR(
    apiBaseUrl + login,
    (url: string) =>
      axios.post(url, {
        username: params.username(),
        password: params.password()
      }),
    {
      revalidateOnMount: false,
      revalidateOnFocus: false
    }
  )

  return {
    loginRes,
    loginError,
    mutateLogin
  }
}
