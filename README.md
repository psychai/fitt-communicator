# Fitt Communicator

This is a package that retrieves the person ID from fitt, and returns it to the consuming system, for later usage.

## Installation

Add the repository to your composer.json file:
```bash
"repositories": [
    {
        "url": "https://github.com/psychai/fitt-communicator.git",
        "type": "git"
    }
],
```

Run the following command:
```bash
composer require psychai/fitt-communicator
```

This package uses auto-discovery.

## Environment Setup
The following variables need to be set in your .env file:
```bash
FITT_COMMUNICATOR_CLIENT_ID=
FITT_COMMUNICATOR_CLIENT_SECRET=
FITT_COMMUNICATOR_CALLBACK_URL=
```
The CLIENT ID and SECRET is set in the Fitt System. The CALLBACK URL is the local endpoint where the system should redirect you back with the person's ID who is using the system.

In that endpoint, you can add the following code to manage the process:
```php
    $personId = $request->get('pid');
        
    switch($request->get('action')) {
        case 'login':
            // Custom code here
            return redirect(/** Page after login */);

        case 'register':
            // Custom code here
            return redirect(/** Page after registration */);

        case 'assessment':
            // Custom code here
            return redirect(/** Page after assessment */);
            
        case 'assessment_paused':
            // Custom code here
            return redirect(/** Page after assessment_paused */);
    }
```

If you are testing, and have a local copy of the fitt system running, you can add the following variable to point to your local fitt:
```bash
FITT_COMMUNICATOR_BASE_URL=https://fitt.local
```

### Usage

The fitt-communicator automatically registers a `/fitt-communicator/login` route, that will redirect the person to fitt to login/register
and then redirect back to the system to the callback url set in the FITT_COMMUNICATOR_CALLBACK_URL specified.

So to get the process started, simply redirect to the user to the `/fitt-communicator/login` route.

### Feedback

For feedback, feature requests or questions, create a ticket on the GitHub repository.