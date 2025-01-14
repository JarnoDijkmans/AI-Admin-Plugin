import unittest
import requests
from requests.auth import HTTPBasicAuth
import os

# Base URL for the API
BASE_URL = os.getenv('BASE_URL', "https://staging-ai.yookerdesign.nl/wp-json/yooker-ai-admin/v1")
USERNAME = "jarno"
PASSWORD = "Bw6nbKFJQb2cS6qdu9QbF8b4"


class TestUserSubscriptionsEndpoint(unittest.TestCase):

    def test_authenticated_user_with_subscriptions(self):
        """
        Test the endpoint for an authenticated user with active subscriptions.
        """
        url = f"{BASE_URL}/user-subscriptions/"
        auth = HTTPBasicAuth(USERNAME, PASSWORD)

        response = requests.get(url, auth=auth)
        self.assertEqual(response.status_code, 200, "Expected HTTP status 200 for authenticated user")

        data = response.json()
        # print("Response JSON:", data)  

        self.assertIn('status', data, "Response JSON does not contain 'status'")
        self.assertEqual(data['status'], 200, "Expected status 200 in response")
        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertIn('data', data, "Response JSON does not contain 'data'")
        self.assertEqual(data['message'], 'Successfully gathered subscriptions')

        subscriptions = data['data']
        self.assertIsInstance(subscriptions, list, "Expected 'data' to be a list of subscriptions")

        for subscription in subscriptions:
            self.assertIn('id', subscription, "Subscription does not contain 'id'")
            self.assertIn('name', subscription, "Subscription does not contain 'name'")
            self.assertIn('short_description', subscription, "Subscription does not contain 'short_description'")
            self.assertIn('price', subscription, "Subscription does not contain 'price'")
            self.assertIn('status', subscription, "Subscription does not contain 'status'")
            self.assertIn('end_date', subscription, "Subscription does not contain 'end_date'")

    def test_unauthenticated_user(self):
        url = f"{BASE_URL}/user-subscriptions/"

        response = requests.get(url) 
        # print("Response status code:", response.status_code)
        # print("Response JSON:", response.json()) 

        self.assertIn(response.status_code, [403, 401], "Expected HTTP status 403 or 401 for unauthenticated user")

        try:
            data = response.json()
        except ValueError:
            self.fail(f"Response is not valid JSON: {response.text}")

        self.assertIn('code', data, "Response JSON does not contain 'code'")
        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertIn('data', data, "Response JSON does not contain 'data'")
        self.assertIn('status', data['data'], "Response 'data' does not contain 'status'")

        if data['data']['status'] == 403:
            self.assertEqual(data['message'], 'User not authenticated')
        elif data['data']['status'] == 401:
            self.assertEqual(data['message'], 'Authorization header is missing or invalid')



    def test_authenticated_user_without_subscriptions(self):
        url = f"{BASE_URL}/user-subscriptions/"
        auth = HTTPBasicAuth("TestuserWithoutPayments", "3LvGXpjriK4hTx8u6JbOPyMg")

        response = requests.get(url, auth=auth)
        # print("Response status code:", response.status_code)
        # print("Response JSON:", response.json())

        self.assertEqual(response.status_code, 200, "Expected HTTP status 200 for authenticated user")

        data = response.json()

        self.assertIn('status', data, "Response JSON does not contain 'status'")
        self.assertEqual(data['status'], 200, "Expected status 200 in response")
        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertEqual(data['message'], 'Successfully gathered subscriptions')
        self.assertIn('data', data, "Response JSON does not contain 'data'")

        subscriptions = data['data']
        self.assertIsInstance(subscriptions, list, "Expected 'data' to be a list of subscriptions")
        self.assertGreaterEqual(len(subscriptions), 0, "Subscriptions should be empty or have default values")



if __name__ == "__main__":
    unittest.main()
