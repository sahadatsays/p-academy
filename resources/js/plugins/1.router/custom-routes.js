
// 👉 Redirects
export const redirects = [
  {
    path: '/',
    name: 'root',
    redirect: to => {
      const hasToken = useCookie('accessToken').value
      
      console.log(hasToken)

      if (hasToken) {
        return { name: 'root' }
      }
      
      return { name: 'login', query: to.query }
    },
  },
]
