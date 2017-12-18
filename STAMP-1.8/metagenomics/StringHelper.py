def isNumber(s):
  '''
  Check if a string represents a number.
  '''
  
  try:
      float(s)
      return True
  except ValueError:
      return False
