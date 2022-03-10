# Campus50 frontend

## Project setup

```
npm install
```

### Compiles and hot-reloads for development

```
npm run serve
```

### Compiles and minifies for production

```
npm run build
```

### Lints and fixes files

```
npm run lint
```

### Customize configuration

### clear storage

```
// Clear the browser cache data in localStorage when closing the browser window
window.onbeforeunload = function() {
  var storage = window.localStorage;
  storage.clear();
}
```
