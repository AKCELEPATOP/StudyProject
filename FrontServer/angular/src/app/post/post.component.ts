import { Component, OnInit } from '@angular/core';
import {PostService} from "../post.service";
import Post from '../Post';

@Component({
  selector: 'app-post',
  templateUrl: './post.component.html',
  styleUrls: ['./post.component.css']
})
export class PostComponent implements OnInit {

  public postList: Array<Post> = [];
  errorMessage: string;

  constructor(private _postService: PostService) { }

  ngOnInit() {
    this.getPosts();
  }

  getPosts(){
    this._postService.getPosts().subscribe(
        posts => this.postList = posts, error => this.errorMessage = <any> error
    );
  }

}
