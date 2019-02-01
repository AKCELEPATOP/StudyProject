import { Injectable } from '@angular/core';
import {HttpClient, HttpErrorResponse} from '@angular/common/http';

import Post from "./Post"
import {Observable, throwError} from "rxjs";
import {catchError} from "rxjs/operators";
import {headersToString} from "selenium-webdriver/http";

@Injectable({
  providedIn: 'root'
})
export class PostService {

  private baseUri = 'http://front.test/api/post';

  private uriGet = this.baseUri + '/getUserPost';

  private uriPost = this.baseUri;

  private uriProcess = this.baseUri + '/setToProcess';

  constructor(private client: HttpClient) { }

  getPosts() : Observable<any[]>{
    return this.client.get<Post[]>(this.uriGet)
        .pipe(
            catchError(this.handleError)
        );
  }

  addPost(post: Post): Observable<Post>{
    return this.client.post<Post>(this.uriPost, post)
        .pipe(
            catchError(this.handleError)
        )
  }

  setToProcess(id: number): any{

  }

  private handleError(error: HttpErrorResponse){
    if (error.error instanceof ErrorEvent) {
      // A client-side or network error occurred. Handle it accordingly.
      console.error('An error occurred:', error.error.message);
    } else {
      // The backend returned an unsuccessful response code.
      // The response body may contain clues as to what went wrong,
      console.error(
          `Backend returned code ${error.status}, ` +
          `body was: ${error.error}`);
    }
    // return an observable with a user-facing error message
    return throwError(
        'Something bad happened; please try again later.');
  }
}
