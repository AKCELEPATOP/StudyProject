import {Component, OnInit} from '@angular/core';
import {PostService} from "../post.service";
import Post from '../Post';
import {PostStatus} from "../PostStatus";

@Component({
    selector: 'app-post',
    templateUrl: './post.component.html',
    styleUrls: ['./post.component.css']
})
export class PostComponent implements OnInit {

    public postList: Array<Post> = [];
    errorMessage: string;
    PostStatus = PostStatus;

    constructor(private _postService: PostService) {
    }

    ngOnInit() {
        this.getPosts();
    }

    getPosts() {
        this._postService.getPosts().subscribe(
            posts => this.postList = posts.result.posts, error => this.errorMessage = <any>error
        );
    }

    executePost(id: number) {
        this._postService.setToProcess(id)
            .subscribe(post => post, error => this.errorMessage = <any>error
            );
        this.getPosts();
    }
}

