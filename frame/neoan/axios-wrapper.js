

let config = {
    baseURL: '{{base}}api.v1/',
    timeout: 8000,
    headers: {'X-Custom-Header': 'foobar'}
};
if(localStorage.token){
    config.headers.Authorization = `Bearer ${localStorage.token}`;
}

const api = axios.create(config);