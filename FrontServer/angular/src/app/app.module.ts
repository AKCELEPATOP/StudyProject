import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';

import {AppRoutingModule} from './app-routing.module';
import {HttpClientModule} from "@angular/common/http";
import {AppComponent} from './app.component';
import {PostComponent} from './post/post.component';
import {AddPostComponent} from './add-post/add-post.component';
import {PostService} from "./post.service";
import {Routes, RouterModule} from "@angular/router";
import {FormsModule} from "@angular/forms";

const appRoutes: Routes = [
    {path: '', component: PostComponent},
    {path: 'create', component: AddPostComponent}
];


@NgModule({
    declarations: [
        AppComponent,
        PostComponent,
        AddPostComponent
    ],
    imports: [
        BrowserModule,
        RouterModule.forRoot(appRoutes),
        HttpClientModule,
        FormsModule
    ],
    providers: [PostService],
    bootstrap: [AppComponent]
})
export class AppModule {
}
