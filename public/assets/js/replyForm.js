export function ReplyForm(comment_id) {
    return `
        <!-- Comment Form -->
        <form action="/comment" method="post" id="form_${comment_id}">
             <div class="comment-form mb-5">
                <div class="mb-3">
                    <label for="commentContent" class="form-label d-none">Content</label>
                    <textarea class="form-control" id="commentContent" rows="3" placeholder="Leave comment..." name="content"></textarea>
                </div>
        
                <div class="mb-3 d-flex justify-content-between pe-3">
                    <input type="submit" class="btn btn-sm btn-warning" value="Comment!" name="commentSubmit" />
                    <!-- <a href="#reply_${comment_id}" data-comment-id="${comment_id}" data-action="close">Close</a> -->
                </div>
            </div>
            
            <input type="hidden" name="parent_id" value="${comment_id}" />
        </form>
    `;
}