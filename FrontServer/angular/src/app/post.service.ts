import {Injectable, NgModule} from '@angular/core';
import {HttpClientModule, HttpClient, HttpErrorResponse} from '@angular/common/http';

import Responce from "./Responce"
import {Observable, throwError} from "rxjs";
import {catchError} from "rxjs/operators";
import Post from "./Post";
@Injectable({
    providedIn: 'root'
})
export class PostService {

    private baseUri = 'http://front.test/api/post';

    private uriGet = this.baseUri + '/getUserPost';

    private uriPost = this.baseUri;

    private uriProcess = this.baseUri + '/setToProcess';

    constructor(private client: HttpClient) {
    }

    getPosts(): Observable<Responce> {
        return this.client.get<Responce>(this.uriGet)
            .pipe(
                catchError(this.handleError)
            );
    }

    addPost(post: Post): Observable<any> {
        return this.client.post(this.uriPost, post)
            .pipe(
                catchError(this.handleError)
            )
    }

    setToProcess(id: number): Observable<any> {
        return this.client.post(this.uriProcess + '/' + id, null)
            .pipe(
                catchError(this.handleError)
            )
    }

    private handleError(error: HttpErrorResponse) {
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
