import { createRoot } from 'react-dom/client'
import 'tailwindcss/tailwind.css'
import App from '~/pages/App'
import { init } from '~/utils'

const container = document.getElementById('root') as HTMLDivElement
const root = createRoot(container)

root.render(<App />)

init()
