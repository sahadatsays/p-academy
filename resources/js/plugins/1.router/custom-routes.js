
// 👉 Redirects
export const redirects = [
  {
    path: '/',
    name: 'index',
    redirect: to => {
      const hasToken = useCookie('accessToken').value
      if (hasToken) {
        return { name: 'root' }
      }
      
      return { name: 'login', query: to.query }
    },
  },
]
