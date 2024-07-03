export default [
  {
    title: 'tableau de bord',
    to: { name: 'root' },
    icon: { icon: 'tabler-home-2' },
  },
  {
    title: 'Poker Académie',
    icon: { icon: 'tabler-cube-plus' },
    children: [
      {
        title: 'Membres',
        to: { name: 'members' },
      },
      {
        title: 'Affiliations',
        to: { name: 'affiliations' },
      },
      {
        title: 'Commandes',
        to: { name: 'commandes' },
      },
      {
        title: 'Tournois',
        to: { name: 'tournois' },
      },
    ],
  },
  {
    title: 'CMS',
    icon: { icon: 'tabler-device-desktop' },
    children: [
      {
        title: 'Articles',
        to: { name: 'articles' },
      },
      {
        title: 'Tags',
        to: { name: 'tags' },
      },
      {
        title: 'Tags PA',
        to: '',
      },
      {
        title: 'Modules',
        to: '',
      },
      {
        title: 'SEO',
        to: '',
      },
      {
        title: 'Menus',
        to: '',
      },
    ],
  },
  {
    title: 'Users',
    to: { name: 'users' },
    icon: { icon: 'tabler-users' },
  },
  {
    title: 'System',
    icon: { icon: 'tabler-settings-star' },
    children: [
      {
        title: 'Logs Laravel',
        href: '/logs',
        target: '__blank',
      },
      {
        title: 'BDD',
        href: '/adminer',
        target: '__blank',
      },
      {
        title: 'PHP Info',
        href: '/phpinfo',
        target: '__blank',
      },
    ],
  },
]
