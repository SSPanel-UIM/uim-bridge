import { Grid, Card, Button, Box } from '@mui/material'
import preFetch from '~/hooks/preFetch'
import AwaitPrefetch from '~/components/AwaitPrefetch'
import { index, prefetchBaseUrl, login, register } from '~/requests/routes'
import { to } from '~/utils'

function App() {
  const toLogin = to.bind(null, login)
  const toRegister = to.bind(null, register)

  const { data } = preFetch(prefetchBaseUrl + index)

  const appName = () => {
    return !!data && ((data as Record<string, unknown>).appName as string)
  }

  return (
    <AwaitPrefetch loading={!!data}>
      <Grid container spacing={2}>
        <Grid item xs={4}></Grid>
        <Grid item xs={4}>
          <Card className="p-4 mt-20">
            <Box className="text-center">
              <h3>{appName()}</h3>
            </Box>
            <Box className="text-center">
              <Button onClick={toLogin} className="w-40" variant="contained">
                登录
              </Button>
            </Box>
            <Box className="mt-4 text-center">
              <Button onClick={toRegister} className="w-40" variant="contained">
                注册
              </Button>
            </Box>
          </Card>
        </Grid>
        <Grid item xs={4}></Grid>
      </Grid>
    </AwaitPrefetch>
  )
}

export default App
