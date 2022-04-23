import { PropsWithChildren } from 'react'

interface AwaitPrefetch {
  loading: boolean
}

const AwaitPrefetch = (props: PropsWithChildren<AwaitPrefetch>) => {
  if (props.loading) return <main className="bg-white">{props.children}</main>
  else return <main>Loading...</main>
}

export default AwaitPrefetch
