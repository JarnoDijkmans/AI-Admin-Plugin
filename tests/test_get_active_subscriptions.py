import unittest
import requests
from requests.auth import HTTPBasicAuth
import os

# Base URL for the API
BASE_URL = os.getenv('BASE_URL', "https://staging-ai.yookerdesign.nl/wp-json/yooker-ai-admin/v1")
USERNAME = "jarno"  
PASSWORD = "Bw6nbKFJQb2cS6qdu9QbF8b4"  


class TestGetActiveSubscriptions(unittest.TestCase):

    def test_authenticated_user_with_active_subscriptions(self):
        """
        Test the endpoint for an authenticated user with active subscriptions.
        """
        url = f"{BASE_URL}/active-subscriptions/"
        auth = HTTPBasicAuth(USERNAME, PASSWORD)

        response = requests.get(url, auth=auth)
        self.assertEqual(response.status_code, 200, "Expected HTTP status 200 for authenticated user")

        # Parse the response JSON
        data = response.json()
        print("Response JSON:", data)  # Debugging

        # Validate response structure
        self.assertIn('status', data, "Response JSON does not contain 'status'")
        self.assertEqual(data['status'], 200, "Expected status 200 in response")
        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertEqual(data['message'], 'User has active subscriptions')
        self.assertIn('data', data, "Response JSON does not contain 'data'")

        # Validate subscriptions data
        subscriptions = data['data']['subscriptions']
        self.assertIsInstance(subscriptions, list, "Expected 'subscriptions' to be a list")
        self.assertGreater(len(subscriptions), 0, "Expected at least one active subscription")
        for subscription in subscriptions:
            self.assertIsInstance(subscription, str, "Each subscription should be a string")

    def test_authenticated_user_without_active_subscriptions(self):
        """
        Test the endpoint for an authenticated user without active subscriptions.
        """
        url = f"{BASE_URL}/active-subscriptions/"
        auth = HTTPBasicAuth("TestuserWithoutPayments", "3LvGXpjriK4hTx8u6JbOPyMg")

        response = requests.get(url, auth=auth)
        self.assertEqual(response.status_code, 404, "Expected HTTP status 404 for user without active subscriptions")

        # Parse the response JSON
        data = response.json()
        print("Response JSON:", data)  # Debugging

        # Validate response structure
        self.assertIn('status', data, "Response JSON does not contain 'status'")
        self.assertEqual(data['status'], 404, "Expected status 404 in response")
        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertEqual(data['message'], 'User does not have any active subscriptions')

    def test_unauthenticated_user(self):
        """
        Test the endpoint for an unauthenticated user.
        """
        url = f"{BASE_URL}/active-subscriptions/"

        response = requests.get(url)  # No authentication provided
        print("Response status code:", response.status_code)
        print("Response JSON:", response.json())

        self.assertEqual(response.status_code, 401, "Expected HTTP status 401 for unauthenticated user")

        # Parse the response JSON
        data = response.json()

        # Validate error structure
        self.assertIn('status', data['data'], "Response 'data' does not contain 'status'")
        self.assertEqual(data['data']['status'], 401, "Expected status 401 in response")
        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertEqual(data['message'], 'Authorization header is missing or invalid')


if __name__ == "__main__":
    unittest.main()
