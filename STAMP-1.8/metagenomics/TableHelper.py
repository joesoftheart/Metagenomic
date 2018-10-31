import operator
import math

def SortTable(table, cols, bAscending = True, bAbsoluteValue = False, bLog = False):
  ''' 
  Sort a table by multiple columns.
      table: a list of lists (or tuple of tuples) where each inner list 
             represents a row
      cols:  a list (or tuple) specifying the column numbers to sort by
             e.g. (1,0) would sort by column 1, then by column 0
      bAscending: flag indicating if column should be sorted in ascending or
                  descending order
      bAbsoluteValue: flag indicating if sorting should be based on the absolute
                      value of each element in the column
      bLog: flag indicating if log of value should be taken before sorting
  '''
  
  for col in reversed(cols):
    if bLog and bAbsoluteValue:
      f = (lambda a, b: cmp(abs(math.log10(a)), abs(math.log10(b))))
      table = sorted(table, f, key = operator.itemgetter(col), reverse = (not bAscending))
    elif bLog and not bAbsoluteValue:
      f = (lambda a, b: cmp(math.log10(a), math.log10(b)))
      table = sorted(table, f, key = operator.itemgetter(col), reverse = (not bAscending))
    elif not bLog and bAbsoluteValue:
      f = (lambda a, b: cmp(abs(a), abs(b)))
      table = sorted(table, f, key = operator.itemgetter(col), reverse = (not bAscending))
    else:
      table = sorted(table, key = operator.itemgetter(col), reverse = (not bAscending))
      
  return table

def SortTableStrCol(table, col, bAscending = True):
  ''' 
  Sort a table column so it is in alphabetical order
      table: a list of lists (or tuple of tuples) where each inner list 
             represents a row
      col:  a column numbers to sort by
      bAscending: flag indicating if column should be sorted in ascending or
                  descending order
  '''
  
  table = sorted(table, key = lambda row: row[col].lower(), reverse = (not bAscending))
      
  return table

