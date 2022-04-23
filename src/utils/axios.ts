import axios from 'axios'

export const initAxios = () => {
  axios.defaults.validateStatus = function (status) {
    return status < 500
  }
  axios.interceptors.response.use(
    (response) => {
      console.log(response)
      const pathname = new URL(response.request.responseURL).pathname
      if (pathname !== response.config.url) {
        window.location.href = pathname
        return
      }
      return response.data
    },
    (error) => {
      return Promise.reject(error)
    }
  )
}
