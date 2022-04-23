import useSWR from 'swr'
import axios from 'axios'

export default (href: string) => {
  const { data, error } = useSWR(href, axios.get)

  console.log(error)
  console.log(data)

  return {
    data
  }
}
