import {Component, OnInit} from '@angular/core';
import {PostService} from "../post.service";
import {Router} from "@angular/router";

@Component({
    selector: 'app-add-post',
    templateUrl: './add-post.component.html',
    styleUrls: ['./add-post.component.css']
})
export class AddPostComponent implements OnInit {

    method: string ;
    url: string;
    timeExecute: Date;
    body: string;
    errors= [];

    constructor(private _postService: PostService, private router: Router) {
    }

    addPost() {
        let post: any;
        post = {method: this.method,url: this.url,timeExecute: this.timeExecute,body: this.body};
        this._postService.addPost(post).subscribe((result =>{
            this.router.navigate(['/']);
        }), addError => this.errors = addError);
    }

    ngOnInit() {
    }

}
