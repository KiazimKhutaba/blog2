export default class ApiBase
{
    async baseXhr(url, method, headers, body) {
        const _headers = { 'X-Requested-With': 'XMLHttpRequest'};
        const res = await fetch(url, { method, ..._headers, ...headers, body });
        return await res.json();
    }

    async get(url, headers = {}, body = null) {
        return await this.baseXhr(url, 'GET', headers, body);
    }

    async post(url, headers = {}, body = null) {
        return await this.baseXhr(url, 'POST', headers, body);
    }

    async delete(url, headers = {}, body = null) {
        return await this.baseXhr(url, 'DELETE', headers, body);
    }

    async put(url, headers = {}, body = null) {
        return await this.baseXhr(url, 'PUT', headers, body);
    }
}
