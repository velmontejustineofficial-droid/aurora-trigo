export default class Router {
    routes = [];

    addRoute(path, content) {
        this.routes.push({ path, content });
    }
    getRoute(path) {
        return this.routes.find(route => route.path === path);
    }
}