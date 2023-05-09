export function CommentWrapper(comment) {
    return `
        <div class="comment-wrapper" id="#comment_${comment.id}">
            <div class="card mb-3 comment">
                <div class="card-header d-flex justify-content-between">
                    <span class="">${comment.author}</span>
                    <span>${comment.created_at}</span>
                </div>
                <div class="card-body">
                    ${comment.content}
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <span>Replies</span>
                    <span>
                        <a href="#remove_${comment.parent_id}" class="text-danger"  data-comment-id="${comment.id}" data-action="remove">Remove</a>
                        <a href="#reply_${comment.parent_id}" data-comment-id="${comment.parent_id}" data-action="reply">Reply</a>
                    </span>
                </div>
                
            </div>
        <div>
    `;
}