import ApiBase from "../ApiBase.js";

export default class CommentApi extends ApiBase
{
    async create(post_id, data) {
        return await this.post(`/post/${post_id}/comment`, {}, data);
    }

    async remove(id) {
        return await  this.delete(`/comment/${id}/remove`)
    }
}