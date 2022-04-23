import { Grid, Card, Button, Box, TextField, Snackbar } from '@mui/material'
import preFetch from '~/hooks/preFetch'
import AwaitPrefetch from '~/components/AwaitPrefetch'
import { prefetchBaseUrl, login, user, apiBaseUrl } from '~/requests/routes'
import { to } from '~/utils'
import { useInput } from '~/hooks/useInput'
import useSWR from 'swr'
import axios from 'axios'
import { Fragment, useState } from 'react'
import { useSnackbar } from '~/hooks/useSnackbar'
import { useLogin } from '~/requests/auth'

function App() {
  const { data } = preFetch(prefetchBaseUrl + login)

  const { value: username, onInput: usernameChange } = useInput('')
  const { value: password, onInput: passwordChange } = useInput('')
  const { open, show, hide } = useSnackbar()

  const { mutateLogin, loginRes, loginError } = useLogin({
    username: () => username,
    password: () => password
  })

  const doLogin = async () => {
    await mutateLogin()
    if (loginRes?.data.success) to(user)
    if (loginError) show()
  }

  return (
    <Fragment>
      <AwaitPrefetch loading={!!data}>
        <Grid container spacing={2}>
          <Grid item xs={4}></Grid>
          <Grid item xs={4}>
            <Card className="p-4 mt-20">
              <Box className="text-center">
                <h3>登录</h3>
              </Box>
              <Box className="text-center mt-4">
                <TextField
                  label="用户名"
                  size="small"
                  variant="outlined"
                  onInput={usernameChange}
                />
              </Box>
              <Box className="text-center mt-4">
                <TextField
                  label="密码"
                  size="small"
                  variant="outlined"
                  onInput={passwordChange}
                />
              </Box>
              <Box className="text-center mt-4">
                <Button onClick={doLogin} className="w-40" variant="contained">
                  登录
                </Button>
              </Box>
            </Card>
          </Grid>
          <Grid item xs={4}></Grid>
        </Grid>
      </AwaitPrefetch>
      <Snackbar open={open} onClose={hide} message="登录失败"></Snackbar>
    </Fragment>
  )
}

export default App
