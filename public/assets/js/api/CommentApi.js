
export default class CommentApi
{
    /**
     *
     * @param apiBase {ApiBase}
     */
    constructor(apiBase) {
        this.apiBase = apiBase;
    }

    async create(post_id, data) {
        return await this.apiBase.post(`/post/${post_id}/comment`, {}, data);
    }

    async remove(id) {
        return await  this.apiBase.delete(`/comment/${id}/remove`)
    }
}