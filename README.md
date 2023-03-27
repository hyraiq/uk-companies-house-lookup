hyraiq/uk-companies-house-lookup
================

A PHP SDK to validate UK Company Registration Number (CRNs) and verify them with the
[UK Companies House Public Data API](https://developer-specs.company-information.service.gov.uk/companies-house-public-data-api/reference).
The difference between validation and verification can be outlined as follows:

- Validation uses a regular expression to check that a given number is a valid CRN. This _does not_ contact the API to
  ensure that the given CRN is assigned to a business
- Verification contacts the Companies House through their API to retrieve information registered against the CRN. It
  will tell you if the CRN actually belongs to a business.

In order to use the API (only necessary for verification), you'll need to
[register an account](https://identity.company-information.service.gov.uk/user/register) to receive an API key.


## Type safety

The SDK utilises the [Symfony Serializer](https://symfony.com/doc/current/components/serializer.html) and the
[Symfony Validator](https://symfony.com/doc/current/components/validator.html) to deserialize and validate data returned
from the API in order to provide a valid [CompanyResponse](./src/Model/CompanyResponse.php) model. This means that if
you receive a response from the SDK, it is guaranteed to be valid.

Invalid responses from the API fall into three categories, which are handled with exceptions:

- `BusinessRegistryConnectionException.php`: Unable to connect to the API, or the API returned an unexpected response
- `BusinessNumberInvalidException.php`: The CRN is invalid (i.e. validation failed)
- `BusinessNumberNotFoundException.php`: The CRN is valid, however it is not assigned to a business (i.e. verification failed)


## Usage

### Installation

```shell
$ composer require hyraiq/uk-companies-house-lookup
```

### Configuration with Symfony

In `services.yaml`, you need to pass you ABR API key to the `ApiClient` and register the `ApiClient` with the
`ApiClientInterface`:

```yaml
Hyra\UkCompaniesHouseLookup\ApiClientInterface: '@Hyra\UkCompaniesHouseLookup\ApiClient'
Hyra\UkCompaniesHouseLookup\ApiClient:
    arguments:
        $apiKey: "%env(UK_COMPANIES_HOUSE_API_KEY)%"
```

You can then inject the `ApiClientInterface` directly into your controllers/services.

```php
class VerifyController extends AbtractController
{
    public function __construct(
        private ApiClientInterface $apiClient,
    ) {
    }
    
    // ...  
}
```

### Configuration outside Symfony

If you're not using Symfony, you'll need to instantiate the API client yourself, which can be registered in your service
container or just used directly. We have provided some helpers in the `Dependencies` class in order to create the
Symfony Serializer and Validator with minimal options.

```php
use Hyra\UkCompaniesHouseLookup\Dependencies;
use Hyra\UkCompaniesHouseLookup\ApiClient;

$apiKey = '<insert your API key here>'

// Whichever http client you choose
$httpClient = new HttpClient();

$denormalizer = Dependencies::serializer();
$validator = Dependencies::validator();

$apiClient = new ApiClient($denormalizer, $validator, $httpClient, $apiKey);
```

### Looking up a business number

Once you have configured your `ApiClient` you can look up an individual CRN. Note, this will validate the CRN before
calling the API in order to prevent unnecessary API requests.

```php
$number = '04264132';

try {
    $response = $apiClient->lookupNumber($number);
} catch (BusinessRegistryConnectionException $e) {
    die($e->getMessage())
} catch (BusinessNumberInvalidException) {
    die('Invalid business number');
} catch (BusinessNumberNotFoundException) {
    die('Business number not found');
}

echo $response->companyNumber; // 04264132
echo $response->companyName; // BENTLEY CARS LIMITED
echo $response->status; // active
```


## Testing

In automated tests, you can replace the `ApiClient` with the `StubApiClient` in order to mock responses from the API.
There is also the `BusinessNumberFaker` which you can use during tests to get both valid and invalid CRNs.

```php
use Hyra\UkCompaniesHouseLookup\Stubs\BusinessNumberFaker;
use Hyra\UkCompaniesHouseLookup\Stubs\StubApiClient;

$stubClient = new StubApiClient();

$stubClient->lookupNumber(BusinessNumberFaker::invalidBusinessNumber()); // BusinessNumberInvalidException - Note, the stub still uses the validator

$stubClient->lookupNumber(BusinessNumberFaker::validBusinessNumber()); // LogicException - You need to tell the stub how to respond to specific queries

$businessNumber = BusinessNumberFaker::validBusinessNumber();
$stubClient->addNotFoundBusinessNumbers($businessNumber);
$stubClient->lookupNumber($businessNumber); // BusinessNumberNotFoundException

$businessNumber = BusinessNumberFaker::validBusinessNumber();
$mockResponse = MockCompanyResponse::valid();
$mockResponse->businessNumber = $businessNumber;

$stubClient->addMockResponse($mockResponse);
$response = $stubClient->lookupNumber($businessNumber); // $response === $mockResponse
```


## Contributing

All contributions are welcome! You'll need [docker](https://docs.docker.com/engine/install/) installed in order to
run tests and CI processes locally. These will also be run against your pull request with any failures added as
GitHub annotations in the Files view.

```shell
# First build the required docker container
$ docker compose build

# Then you can install composer dependencies
$ docker compose run php ./composer.phar install

# Now you can run tests and other tools
$ docker compose run php make (fix|psalm|phpstan|phpunit)
```

In order for you PR to be accepted, it will need to be covered by tests and be accepted by:

- [php-cs-fixer](https://github.com/FriendsOfPhp/PHP-CS-Fixer)
- [psalm](https://github.com/vimeo/psalm/)
- [phpstan](https://github.com/phpstan/phpstan)
