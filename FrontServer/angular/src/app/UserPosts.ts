import Post from "./Post";

export default class {
    constructor(public posts: Array<Post>, public userId: number, public postsCount: number, public requestsCount: number, public averageSpeed: number) {
    }

}