import ApiBase from "./ApiBase.js";
import {ReplyForm} from "./replyForm.js";
import {CommentWrapper} from "./commentWrapper.js";
import CommentApi from "./api/CommentApi.js";

document.addEventListener('DOMContentLoaded', function ()
{
    const commentAddTopForm = document.querySelector('#commentAddTopForm');
    const comments = document.querySelector('.comments');
    const post_id = comments.dataset.postId;
    const actions = Object.freeze({  reply: 'reply', close: 'close', remove: 'remove' });
    const api = new ApiBase();
    const commentApi = new CommentApi(api);


    commentAddTopForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const form = new FormData(e.target);
        const data = await commentApi.create(form.get('post_id'), form);

        if(data) {
            const commentDiv = document.createElement('div')
            commentDiv.innerHTML = CommentWrapper(data);

            const cw = document.querySelector('.comment-wrapper');

            if(!cw) {
                const cw = document.createElement('div');
                cw.setAttribute('class', 'comment-wrapper');
                document.querySelector('.comments').appendChild(cw);
                cw.appendChild(commentDiv);
            }
            else {
                cw.appendChild(commentDiv);
            }
        }
        //console.log(data);
    });

    comments.addEventListener('click', async (e) => {

        if (e.target instanceof HTMLAnchorElement) {
            const anchor = e.target;
            const comment_id = e.target.dataset.commentId;
            const action = e.target.dataset.action;
            const wrapper = e.target.closest('.comment-wrapper');
            const divElement = document.createElement('div');

            switch (action) {
                case actions.reply:

                    // if we already added reply form, we can't add any reply form anymore
                    if (wrapper.childNodes.length === 4) return;

                    divElement.innerHTML = ReplyForm(comment_id);
                    wrapper.appendChild(divElement);
                    anchor.dataset.action = actions.close;
                    anchor.innerText = 'Close';

                    wrapper.addEventListener('submit', async (e) => {
                        e.preventDefault();

                        const form = new FormData(e.target);
                        let data = await api.post(`/post/${post_id}/comment`, {}, form);

                        if(data) {
                            wrapper.removeChild(wrapper.lastChild);
                            anchor.dataset.action = actions.reply;
                            anchor.innerText = 'Reply';

                            const commentDiv = document.createElement('div')
                            commentDiv.innerHTML = CommentWrapper(data);
                            wrapper.appendChild(commentDiv)
                        }
                    });


                    break;

                case actions.close:
                    wrapper.removeChild(wrapper.lastChild);
                    anchor.dataset.action = actions.reply;
                    anchor.innerText = 'Reply';
                    break;

                case actions.remove:

                    if(!confirm(`Do you confirm deletion of comment with id ${comment_id} ?`)) return;

                    let response = await commentApi.remove(comment_id);
                    let result = await response.json();

                    if (result === 1) {
                        const commentElement = document.getElementById(`#comment_${comment_id}`);
                        comments.removeChild(commentElement);
                    }


                    break;
            }
        }
    });

});
