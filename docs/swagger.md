# API Documentation (Swagger)
This document describes how to access and use the interactive [Swagger UI](https://swagger.io/) to explore and test our API.

## Accessing the Local Swagger UI
You can browse the available endpoints, view request/response schemas, and execute API calls directly from your web browser using our local Swagger service.

* Local URL: [http://localhost/api/doc](http://localhost/api/doc)

### Authorization
If you need to test protected endpoints directly within Swagger:

1. Obtain an access token (e.g., via the `getToken` endpoint or Postman).
2. Click the **Authorize** button (usually a padlock icon) at the top right of the Swagger UI.
3. Paste your token into the input field.
   > * For standard access tokens, use: `<your_token>`
   > * For API Key authentication, use: `ApiKey <your_token>`

4. Click **Authorize** to apply it to all subsequent requests.
