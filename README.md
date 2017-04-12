# api

##How to use

Jobs:

- GET /jobs/list: 
-- No parameters required; 
-- Return all jobs;

- GET /jobs/{id}
-- Parameters: {id}: number (required);
-- Return job info;

- PUT /jobs/{id}
-- Parameters: {id}: number (required);
               "position": string with min length 3;
               "description": string with min length 3;
               Required: at least one from "position", "description"
-- Return job id on success;

- POST /jobs/
-- Parameters: "position": string with min length 3 (required);
               "description": string with min length 3 (required);
-- Return job id on success;

- DELETE /jobs/{id}
-- Parameters: {id}: number (required);
-- Return job id of the deleted job;

Candidates:

- GET /candidates/list: 
-- No parameters required; 
-- Return all candidates;

- GET /candidates/review/{id}
-- Parameters: {id}: number (required);
-- Return candidate info;

- PUT /candidates/review/{id}
-- Parameters: {id}: number (required);
               "name": string with min length 3 (required);
-- Return candidate id on success;

- POST /candidates/review/
-- Parameters: "name": string with min length 3 (required);
               "job_id": valid job id (required);
-- Return candidate id on success;

- DELETE /candidates/review/{id}
-- Parameters: {id}: number (required);
-- Return id of the deleted candidate;

- GET /candidates/search/{id}
-- Parameters: {id}: number, job id (required);
-- Return all candidates who applied for the given job;

- GET /candidates/search/{name}
-- Parameters: {name}: string, full candidate name (required);
-- Return all candidates with the given name;
--------------------------------------------------------------------------

## Future improvements

- add paginating on lists
- add error message code
--------------------------------------------------------------------------

api.sql contains the required database structure and sample data 

The directories controllers and libs are placed outside public_html. 
Files outside public directory cannot be accessed via apache directly for example.