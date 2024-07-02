
export const setupGuards = router => {
  // 👉 router.beforeEach
  // Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards

  router.beforeEach((to, from, next) => {
    /**
     * If it's a public route, continue navigation. This kind of pages are allowed to visited by login & non-login users. Basically, without any restrictions.
     */
    if (to.meta.public)
      return 

    const isLoggedIn = !!(useCookie('accessToken').value && useCookie('userData').value)

    const publicPages = ['/login']
    const authRequired = !publicPages.includes(to.path)
   
    if (isLoggedIn && to.name == 'login') {
      next({ name: 'root' })
    }
    if (authRequired && !isLoggedIn) {
      next({
        name: 'login',
        query: {
          ...to.query,
          to: to.fullPath !== '/' ? to.path : undefined,
        },
      })
    } else {
      next()
    }
  })
}
