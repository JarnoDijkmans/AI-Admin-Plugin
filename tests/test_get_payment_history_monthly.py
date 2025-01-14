import unittest
import requests
from requests.auth import HTTPBasicAuth
import os

# Base URL for the API
BASE_URL = os.getenv('BASE_URL', "https://staging-ai.yookerdesign.nl/wp-json/yooker-ai-admin/v1")
USERNAME = "jarno"  
PASSWORD = "Bw6nbKFJQb2cS6qdu9QbF8b4"  


class TestGetPaymentHistoryMonthly(unittest.TestCase):

    def test_authenticated_user_with_payments(self):
        """
        Test the endpoint for an authenticated user with payment history.
        """
        url = f"{BASE_URL}/get-payment-history-monthly/"
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
        self.assertEqual(data['message'], 'Payment history retrieved successfully')
        self.assertIn('data', data, "Response JSON does not contain 'data'")

        # Validate payment data
        payments = data['data']
        self.assertIsInstance(payments, list, "Expected 'data' to be a list of payments")
        self.assertGreater(len(payments), 0, "Expected at least one payment in the response")

        # Check the structure of each payment
        for payment in payments:
            self.assertIn('sub_type', payment, "Payment does not contain 'sub_type'")
            self.assertIn('total_value', payment, "Payment does not contain 'total_value'")
            self.assertIn('month', payment, "Payment does not contain 'month'")
            self.assertIn('year', payment, "Payment does not contain 'year'")

    def test_authenticated_user_without_payments(self):
        """
        Test the endpoint for an authenticated user without payment history.
        """
        url = f"{BASE_URL}/get-payment-history-monthly/"
        auth = HTTPBasicAuth("TestuserWithoutPayments", "3LvGXpjriK4hTx8u6JbOPyMg")

        response = requests.get(url, auth=auth)
        self.assertEqual(response.status_code, 200, "Expected HTTP status 200 for authenticated user")

        # Parse the response JSON
        data = response.json()
        print("Response JSON:", data)  # Debugging

        # Validate response structure
        self.assertIn('status', data, "Response JSON does not contain 'status'")
        self.assertEqual(data['status'], 200, "Expected status 200 in response")
        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertEqual(data['message'], 'No payment history found for this user')

        # Check that 'data' exists but is None
        self.assertIn('data', data, "Response JSON does not contain 'data'")
        self.assertIsNone(data['data'], "Expected 'data' to be None for users without payments")


    def test_unauthenticated_user(self):
        """
        Test the endpoint for an unauthenticated user.
        """
        url = f"{BASE_URL}/get-payment-history-monthly/"

        response = requests.get(url)  # No authentication provided
        print("Response status code:", response.status_code)
        print("Response JSON:", response.json())  # Debugging

        self.assertEqual(response.status_code, 401, "Expected HTTP status 401 for unauthenticated user")

        # Parse the response JSON
        data = response.json()

        # Validate error structure
        self.assertIn('status', data['data'], "Response 'data' does not contain 'status'")
        self.assertEqual(data['data']['status'], 401, "Expected status 401 in response")
        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertEqual(data['message'], 'Authorization header is missing or invalid')

    def test_pagination(self):
        """
        Test the endpoint for pagination support.
        """
        url = f"{BASE_URL}/get-payment-history-monthly/?page=2&per_page=5"
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
        self.assertEqual(data['message'], 'Payment history retrieved successfully')
        self.assertIn('data', data, "Response JSON does not contain 'data'")

        # Validate payment data
        payments = data['data']
        self.assertIsInstance(payments, list, "Expected 'data' to be a list of payments")


if __name__ == "__main__":
    unittest.main()
